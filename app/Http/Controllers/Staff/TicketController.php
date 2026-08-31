<?php

namespace App\Http\Controllers\Staff;

use App\Enums\ItemCondition;
use App\Enums\SenderType;
use App\Enums\TicketStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Staff\IndexTicketsRequest;
use App\Http\Requests\Staff\ProcessRefundRequest;
use App\Http\Requests\Staff\StoreTicketCommunicationRequest;
use App\Http\Requests\Staff\StoreTicketConditionRequest;
use App\Http\Requests\Staff\TransitionTicketRequest;
use App\Models\ReturnTicket;
use App\Services\TicketWorkflow;
use DomainException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class TicketController extends Controller
{
    /**
     * Daftar tiket retur: filter status, pencarian, dan pagination.
     */
    public function index(IndexTicketsRequest $request): View
    {
        $data = $request->validated();

        $tickets = ReturnTicket::query()
            ->with(['customer', 'product', 'assignee'])
            ->when(
                $data['status'] ?? null,
                fn ($query, $status) => $query->where('status', TicketStatus::from($status)),
            )
            ->when(
                trim((string) ($data['search'] ?? '')) !== '',
                function ($query) use ($data) {
                    $term = trim((string) $data['search']);

                    $query->where(function ($inner) use ($term) {
                        $inner->where('ticket_number', 'like', "%{$term}%")
                            ->orWhereHas('customer', fn ($customer) => $customer->where('name', 'like', "%{$term}%"))
                            ->orWhereHas('product', fn ($product) => $product->where('name', 'like', "%{$term}%"));
                    });
                },
            )
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('staff.tickets.index', [
            'tickets' => $tickets,
            'statuses' => TicketStatus::cases(),
            'selectedStatus' => $data['status'] ?? null,
            'search' => $data['search'] ?? '',
        ]);
    }

    /**
     * Detail tiket: riwayat, komunikasi, bukti, live chat, dan aksi transisi.
     */
    public function show(ReturnTicket $ticket): View
    {
        $ticket->load([
            'customer',
            'product',
            'assignee',
            'communications.sender',
            'statusHistories.user',
            'chatMessages',
            'evidences',
        ]);

        return view('staff.tickets.show', [
            'ticket' => $ticket,
            'evidences' => $ticket->evidences,
            'availableTransitions' => app(TicketWorkflow::class)->availableTransitions($ticket, auth()->user()),
            'refundMethods' => ProcessRefundRequest::REFUND_METHODS,
            'warehouseVerdictStatus' => TicketStatus::PemeriksaanGudang,
            'refundProcessingStatus' => TicketStatus::RefundDiproses,
        ]);
    }

    /**
     * Transisi status generik — selalu melewati TicketWorkflow.
     */
    public function transition(TransitionTicketRequest $request, ReturnTicket $ticket): RedirectResponse
    {
        $data = $request->validated();

        try {
            app(TicketWorkflow::class)->transition(
                $ticket,
                TicketStatus::from($data['status']),
                $request->user(),
                $data['note'] ?? null,
            );
        } catch (DomainException $exception) {
            return back()->withInput()->with('error', $exception->getMessage());
        }

        return redirect()
            ->route('staff.tickets.show', $ticket)
            ->with('success', "Status tiket {$ticket->ticket_number} berhasil diubah menjadi {$data['status']}.");
    }

    /**
     * Vonis gudang: Layak -> Refund Diproses, Tidak Layak -> Selesai.
     */
    public function condition(StoreTicketConditionRequest $request, ReturnTicket $ticket): RedirectResponse
    {
        $condition = $request->itemCondition();
        $target = $condition === ItemCondition::Layak
            ? TicketStatus::RefundDiproses
            : TicketStatus::Selesai;

        try {
            app(TicketWorkflow::class)->transition(
                $ticket,
                $target,
                $request->user(),
                $request->validated('note'),
                $condition,
            );
        } catch (DomainException $exception) {
            return back()->withInput()->with('error', $exception->getMessage());
        }

        return redirect()
            ->route('staff.tickets.show', $ticket)
            ->with('success', "Vonis '{$condition->value}' tersimpan; tiket {$ticket->ticket_number} kini berstatus {$target->value}.");
    }

    /**
     * Penyelesaian refund (Manajemen/Admin): metode + bukti wajib, lalu status Selesai.
     */
    public function refund(ProcessRefundRequest $request, ReturnTicket $ticket): RedirectResponse
    {
        $data = $request->validated();

        $path = $request->file('refund_proof')->store('refunds/'.$ticket->ticket_number, 'public');

        $ticket->forceFill([
            'refund_method' => $data['refund_method'],
            'refund_proof_path' => $path,
        ])->save();

        try {
            app(TicketWorkflow::class)->transition(
                $ticket,
                TicketStatus::Selesai,
                $request->user(),
                $data['note'] ?? null,
            );
        } catch (DomainException $exception) {
            Storage::disk('public')->delete($path);
            $ticket->forceFill(['refund_method' => null, 'refund_proof_path' => null])->save();

            return back()->withInput()->with('error', $exception->getMessage());
        }

        return redirect()
            ->route('staff.tickets.show', $ticket)
            ->with('success', "Refund tiket {$ticket->ticket_number} selesai diproses ({$data['refund_method']}).");
    }

    /**
     * Tambah catatan/komunikasi pada tiket (internal atau ke pelanggan).
     */
    public function communicate(StoreTicketCommunicationRequest $request, ReturnTicket $ticket): RedirectResponse
    {
        $ticket->communications()->create([
            'sender_id' => $request->user()->id,
            'sender_type' => SenderType::Staf,
            'message' => $request->validated('message'),
            'is_internal' => $request->boolean('is_internal'),
        ]);

        return redirect()
            ->route('staff.tickets.show', $ticket)
            ->with('success', 'Komunikasi berhasil ditambahkan.');
    }

    /**
     * Aktifkan/nonaktifkan live chat untuk tiket.
     */
    public function toggleChat(ReturnTicket $ticket): RedirectResponse
    {
        $ticket->update(['chat_active' => ! $ticket->chat_active]);

        $message = $ticket->chat_active
            ? 'Live chat diaktifkan untuk tiket ini.'
            : 'Live chat dinonaktifkan untuk tiket ini.';

        return back()->with('success', $message);
    }
}