<?php

namespace App\Events;

use App\Models\ReturnTicket;
use App\Models\User;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TicketStatusChanged
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public ReturnTicket $ticket,
        public ?User $actor = null,
        public ?string $note = null,
    ) {
    }
}
