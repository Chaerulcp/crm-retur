<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TicketStatusHistory extends Model
{
    public const UPDATED_AT = null;

    protected $fillable = [
        'return_ticket_id',
        'user_id',
        'from_status',
        'to_status',
        'note',
    ];

    public function returnTicket(): BelongsTo
    {
        return $this->belongsTo(ReturnTicket::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
