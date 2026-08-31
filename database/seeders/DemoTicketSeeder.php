<?php

namespace Database\Seeders;

use App\Enums\ItemCondition;
use App\Enums\SenderType;
use App\Enums\TicketStatus;
use App\Models\ChatMessage;
use App\Models\Customer;
use App\Models\Product;
use App\Models\ReturnTicket;
use App\Models\User;
use App\Services\TicketWorkflow;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DemoTicketSeeder extends Seeder
{
    public function run(): void
    {
        if (ReturnTicket::query()->exists()) {
            return;
        }

        $customers = Customer::factory()->count(4)->create();
        $products = Product::all();
        $cs = User::where('email', 'cs@tokokita.com')->firstOrFail();
        $gudang = User::where('email', 'gudang@tokokita.com')->firstOrFail();
        $finance = User::where('email', 'finance@tokokita.com')->firstOrFail();

        $workflow = new TicketWorkflow;

        // Rencana demo: [jumlah tiket, jalur status yang ditempuh]
        $plans = [
            ['count' => 3, 'steps' => []],
            ['count' => 2, 'steps' => [TicketStatus::Diverifikasi]],
            ['count' => 1, 'steps' => [TicketStatus::Diverifikasi, TicketStatus::Disetujui]],
            ['count' => 2, 'steps' => [TicketStatus::Diverifikasi, TicketStatus::Disetujui, TicketStatus::MenungguBarang]],
            ['count' => 1, 'steps' => [TicketStatus::Diverifikasi, TicketStatus::Disetujui, TicketStatus::MenungguBarang, TicketStatus::BarangDiterima]],
            ['count' => 1, 'steps' => [TicketStatus::Diverifikasi, TicketStatus::Disetujui, TicketStatus::MenungguBarang, TicketStatus::BarangDiterima, TicketStatus::PemeriksaanGudang]],
            ['count' => 1, 'steps' => [TicketStatus::Diverifikasi, TicketStatus::Disetujui, TicketStatus::MenungguBarang, TicketStatus::BarangDiterima, TicketStatus::PemeriksaanGudang, TicketStatus::RefundDiproses], 'condition' => ItemCondition::Layak],
            ['count' => 1, 'steps' => [TicketStatus::Diverifikasi, TicketStatus::Disetujui, TicketStatus::MenungguBarang, TicketStatus::BarangDiterima, TicketStatus::PemeriksaanGudang, TicketStatus::RefundDiproses, TicketStatus::Selesai], 'condition' => ItemCondition::Layak, 'refund' => true],
            ['count' => 1, 'steps' => [TicketStatus::Diverifikasi, TicketStatus::Disetujui, TicketStatus::MenungguBarang, TicketStatus::BarangDiterima, TicketStatus::PemeriksaanGudang, TicketStatus::Selesai], 'condition' => ItemCondition::TidakLayak],
            ['count' => 1, 'steps' => [TicketStatus::Ditolak]],
        ];

        $sequence = 0;

        foreach ($plans as $plan) {
            for ($i = 0; $i < $plan['count']; $i++) {
                $sequence++;
                $customer = $customers->random();

                $ticket = ReturnTicket::factory()->create([
                    'customer_id' => $customer->id,
                    'product_id' => $products->random()->id,
                    'ticket_number' => 'RET-'.now()->format('Ymd').'-'.str_pad((string) $sequence, 4, '0', STR_PAD_LEFT),
                    'tracking_token' => Str::random(40),
                    'created_at' => now()->subDays(random_int(0, 14))->subHours(random_int(0, 23)),
                ]);

                foreach ($plan['steps'] as $step) {
                    $actor = match (true) {
                        $step === TicketStatus::RefundDiproses && isset($plan['condition']) && $plan['condition'] === ItemCondition::Layak => $gudang,
                        $step === TicketStatus::Selesai && ($plan['refund'] ?? false) => $finance,
                        $step === TicketStatus::BarangDiterima, $step === TicketStatus::PemeriksaanGudang, $step === TicketStatus::Selesai => $gudang,
                        default => $cs,
                    };

                    $workflow->transition(
                        $ticket,
                        $step,
                        $actor,
                        note: 'Catatan demo untuk status '.$step->value.'.',
                        condition: ($step === TicketStatus::RefundDiproses || $step === TicketStatus::Selesai) ? ($plan['condition'] ?? null) : null,
                    );
                }

                if ($plan['refund'] ?? false) {
                    $ticket->forceFill([
                        'refund_method' => 'Transfer Bank',
                        'refund_proof_path' => null,
                    ])->save();
                }
            }
        }

        // Satu tiket dengan live chat aktif untuk demo.
        $chatTicket = ReturnTicket::query()->where('status', TicketStatus::MenungguBarang)->first();

        if ($chatTicket) {
            $chatTicket->update(['chat_active' => true]);

            ChatMessage::create([
                'return_ticket_id' => $chatTicket->id,
                'sender_type' => ChatMessage::SENDER_CUSTOMER,
                'sender_name' => $chatTicket->customer->name,
                'message' => 'Halo, barang retur saya sudah dikirim kemarin. Kira-kira kapan sampai?',
            ]);

            ChatMessage::create([
                'return_ticket_id' => $chatTicket->id,
                'sender_type' => ChatMessage::SENDER_STAFF,
                'sender_id' => $cs->id,
                'sender_name' => $cs->name,
                'message' => 'Halo! Terima kasih sudah mengirim barang. Kami akan kabari begitu paket diterima tim gudang ya.',
            ]);

            $chatTicket->communications()->create([
                'sender_id' => null,
                'sender_type' => SenderType::Pelanggan,
                'message' => 'Pelanggan mengirim pesan melalui live chat.',
                'is_internal' => true,
            ]);
        }
    }
}
