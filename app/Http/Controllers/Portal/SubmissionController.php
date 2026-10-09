<?php

namespace App\Http\Controllers\Portal;

use App\Enums\ItemCondition;
use App\Enums\SenderType;
use App\Enums\TicketStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Portal\StoreReturnTicketRequest;
use App\Mail\TicketSubmittedMail;
use App\Models\Customer;
use App\Models\Product;
use App\Models\ReturnTicket;
use App\Services\TicketNumberGenerator;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class SubmissionController extends Controller
{
    /**
     * Formulir pengajuan retur.
     */
    public function create(): View
    {
        $products = Product::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('portal.create', ['products' => $products]);
    }

    /**
     * Simpan pengajuan retur baru dari pelanggan (tanpa login).
     */
    public function store(StoreReturnTicketRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $ticket = DB::transaction(function () use ($request, $data): ReturnTicket {
            $customer = Customer::firstOrCreate(
                ['email' => $data['email']],
                ['name' => $data['customer_name'], 'phone' => $data['phone'] ?? null],
            );

            $ticket = ReturnTicket::create([
                'ticket_number' => TicketNumberGenerator::generate(),
                'customer_id' => $customer->id,
                'product_id' => $data['product_id'],
                'invoice_number' => $data['invoice_number'] ?? null,
                'reason' => $data['reason'],
                'refund_method' => $data['refund_method'] ?? null,
                'status' => TicketStatus::Diajukan,
                'item_condition' => ItemCondition::BelumDiterima,
                'tracking_token' => Str::random(40),
                'chat_active' => false,
            ]);

            $ticket->statusHistories()->create([
                'from_status' => null,
                'to_status' => TicketStatus::Diajukan->value,
                'note' => 'Tiket retur diajukan oleh pelanggan melalui portal.',
            ]);

            $ticket->communications()->create([
                'sender_type' => SenderType::Pelanggan,
                'message' => 'Pengajuan retur dibuat oleh pelanggan.',
                'is_internal' => false,
            ]);

            // Simpan bukti pendukung: gambar (maks 5 MB) atau video (maks 50 MB).
            foreach ($request->file('evidences', []) as $file) {
                $kind = str_starts_with((string) $file->getMimeType(), 'video/') ? 'video' : 'image';

                $path = Storage::disk('public')->putFile("evidences/{$ticket->ticket_number}", $file);

                $ticket->evidences()->create([
                    'path' => $path,
                    'original_name' => $file->getClientOriginalName(),
                    'kind' => $kind,
                    'size' => $file->getSize(),
                ]);
            }

            return $ticket;
        });

        \App\Jobs\AnalyzeTicketEvidenceJob::dispatch($ticket);

        Mail::to($ticket->customer->email)->queue(new TicketSubmittedMail($ticket));

        return redirect()->route('portal.success')->with([
            'ticket_number' => $ticket->ticket_number,
            'tracking_token' => $ticket->tracking_token,
        ]);
    }

    /**
     * Halaman konfirmasi sukses (nomor tiket diambil dari session flash).
     */
    public function success(): View|RedirectResponse
    {
        if (! session()->has('ticket_number')) {
            return redirect()->route('portal.create');
        }

        return view('portal.success', [
            'ticketNumber' => session('ticket_number'),
            'trackingToken' => session('tracking_token'),
        ]);
    }
}
