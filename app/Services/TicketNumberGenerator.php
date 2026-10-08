<?php

namespace App\Services;

use App\Models\ReturnTicket;
use Illuminate\Support\Carbon;

class TicketNumberGenerator
{
    /**
     * Membuat nomor tiket unik dengan format RET-YYYYMMDD-XXXX.
     */
    public static function generate(): string
    {
        $date = Carbon::now()->format('Ymd');
        $prefix = "RET-{$date}-";

        $last = ReturnTicket::query()
            ->where('ticket_number', 'like', $prefix.'%')
            ->orderByDesc('ticket_number')
            ->value('ticket_number');

        $sequence = $last ? ((int) substr($last, -4)) + 1 : 1;

        do {
            $candidate = $prefix.str_pad((string) $sequence, 4, '0', STR_PAD_LEFT);
            $sequence++;
        } while (ReturnTicket::query()->where('ticket_number', $candidate)->exists());

        return $candidate;
    }
}
