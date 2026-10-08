<?php

namespace App\Models;

use App\Enums\SenderType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TicketCommunication extends Model
{
    use HasFactory;

    protected $fillable = [
        'return_ticket_id',
        'sender_id',
        'sender_type',
        'message',
        'is_internal',
    ];

    protected function casts(): array
    {
        return [
            'sender_type' => SenderType::class,
            'is_internal' => 'boolean',
        ];
    }

    public function returnTicket(): BelongsTo
    {
        return $this->belongsTo(ReturnTicket::class);
    }

    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }
}
