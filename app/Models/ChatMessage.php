<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChatMessage extends Model
{
    use HasFactory;

    public const SENDER_STAFF = 'staff';

    public const SENDER_CUSTOMER = 'customer';

    protected $fillable = [
        'return_ticket_id',
        'sender_type',
        'sender_id',
        'sender_name',
        'message',
        'is_ai_generated',
    ];

    public function returnTicket(): BelongsTo
    {
        return $this->belongsTo(ReturnTicket::class);
    }

    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function isFromStaff(): bool
    {
        return $this->sender_type === self::SENDER_STAFF;
    }
}
