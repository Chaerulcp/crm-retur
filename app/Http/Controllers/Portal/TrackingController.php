<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Http\Requests\Portal\TrackTicketRequest;
use App\Models\ReturnTicket;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class TrackingController extends Controller
{
    /**
     * Formulir pencarian tiket berdasarkan nomor retur.
     */
    public function index(TrackTicketRequest $request): View|RedirectResponse
    {
        if (! $request->filled('nomor')) {
            return view('portal.tracking.index');
        }

        $ticket = ReturnTicket::query()
            ->where('ticket_number', trim((string) $request->validated('nomor')))
            ->first();

        if (! $ticket) {
            return back()->withInput()->withErrors([
                'nomor' => 'Tiket dengan nomor tersebut tidak ditemukan. Periksa kembali nomor retur Anda.',
            ]);
        }

        return redirect()->route('portal.tracking.show', ['ticket_number' => $ticket->ticket_number]);
    }

    /**
     * Detail pelacakan: status, bukti, riwayat komunikasi publik, timeline, dan live chat.
     */
    public function show(string $ticket_number): View
    {
        $ticket = ReturnTicket::query()
            ->with(['customer', 'product', 'evidences', 'communications', 'statusHistories'])
            ->where('ticket_number', $ticket_number)
            ->firstOrFail();

        return view('portal.tracking.show', ['ticket' => $ticket]);
    }
}
