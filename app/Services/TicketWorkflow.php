<?php

namespace App\Services;

use App\Enums\ItemCondition;
use App\Enums\Role;
use App\Enums\SenderType;
use App\Enums\TicketStatus;
use App\Events\TicketStatusChanged;
use App\Models\ReturnTicket;
use App\Models\User;
use DomainException;
use Illuminate\Support\Facades\DB;

/**
 * State machine alur kerja tiket retur.
 *
 * Seluruh perubahan status tiket WAJIB melewati kelas ini agar
 * aturan transisi per peran selalu konsisten di seluruh aplikasi.
 */
class TicketWorkflow
{
    /**
     * Peta transisi: status awal => daftar status tujuan yang valid.
     *
     * @var array<string, array<int, string>>
     */
    public const TRANSITIONS = [
        'Diajukan' => ['Diverifikasi', 'Ditolak'],
        'Diverifikasi' => ['Disetujui', 'Ditolak'],
        'Disetujui' => ['Menunggu Barang'],
        'Menunggu Barang' => ['Barang Diterima'],
        'Barang Diterima' => ['Pemeriksaan Gudang'],
        'Pemeriksaan Gudang' => ['Refund Diproses', 'Selesai'],
        'Refund Diproses' => ['Selesai'],
    ];

    /**
     * Peran yang diizinkan mengubah tiket ke status tujuan tertentu.
     * Admin selalu diizinkan (diterapkan terpisah).
     *
     * @var array<string, array<int, string>>
     */
    public const ROLE_PERMISSIONS = [
        'Diajukan' => ['Customer Service', 'Admin'],
        'Diverifikasi' => ['Customer Service', 'Admin'],
        'Disetujui' => ['Customer Service', 'Admin'],
        'Ditolak' => ['Customer Service', 'Admin'],
        'Menunggu Barang' => ['Customer Service', 'Admin'],
        'Barang Diterima' => ['Gudang', 'Admin'],
        'Pemeriksaan Gudang' => ['Gudang', 'Admin'],
        'Refund Diproses' => ['Gudang', 'Manajemen', 'Admin'],
        'Selesai' => ['Gudang', 'Manajemen', 'Admin'],
    ];

    /**
     * Cek apakah pengguna boleh memindahkan tiket ke status tujuan.
     */
    public function canTransition(ReturnTicket $ticket, TicketStatus $to, User $user): bool
    {
        $from = $ticket->status;

        if ($from === $to) {
            return false;
        }

        $allowedTargets = self::TRANSITIONS[$from->value] ?? [];
        if (! in_array($to->value, $allowedTargets, true)) {
            return false;
        }

        if ($user->role === Role::Admin) {
            return true;
        }

        $allowedRoles = self::ROLE_PERMISSIONS[$to->value] ?? [];

        return in_array($user->role->value, $allowedRoles, true);
    }

    /**
     * Daftar status tujuan yang tersedia untuk pengguna pada tiket tertentu.
     *
     * @return array<int, TicketStatus>
     */
    public function availableTransitions(ReturnTicket $ticket, User $user): array
    {
        $targets = self::TRANSITIONS[$ticket->status->value] ?? [];

        return collect($targets)
            ->map(fn (string $value) => TicketStatus::from($value))
            ->filter(fn (TicketStatus $to) => $this->canTransition($ticket, $to, $user))
            ->values()
            ->all();
    }

    /**
     * Jalankan transisi status secara atomik:
     * update tiket, catat riwayat, tulis komunikasi, lalu sebarkan event.
     *
     * @throws DomainException
     */
    public function transition(
        ReturnTicket $ticket,
        TicketStatus $to,
        User $user,
        ?string $note = null,
        ?ItemCondition $condition = null,
    ): ReturnTicket {
        if (! $this->canTransition($ticket, $to, $user)) {
            throw new DomainException(
                "Transisi dari '{$ticket->status->value}' ke '{$to->value}' tidak diizinkan untuk peran {$user->role->value}."
            );
        }

        return DB::transaction(function () use ($ticket, $to, $user, $note, $condition) {
            $from = $ticket->status;

            $ticket->status = $to;
            $ticket->assigned_to = $user->id;

            if ($condition !== null) {
                $ticket->item_condition = $condition;
            }

            $ticket->save();

            $ticket->statusHistories()->create([
                'user_id' => $user->id,
                'from_status' => $from->value,
                'to_status' => $to->value,
                'note' => $note,
            ]);

            $ticket->communications()->create([
                'sender_id' => $user->id,
                'sender_type' => SenderType::Staf,
                'message' => $note
                    ? "Status diubah menjadi {$to->value}. Catatan: {$note}"
                    : "Status diubah menjadi {$to->value}.",
                'is_internal' => true,
            ]);

            event(new TicketStatusChanged($ticket, $user, $note));

            return $ticket->refresh();
        });
    }
}
