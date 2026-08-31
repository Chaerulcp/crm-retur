<?php

namespace App\Models;

use App\Enums\ItemCondition;
use App\Enums\TicketStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ReturnTicket extends Model
{
    use HasFactory;

    protected $fillable = [
        'ticket_number',
        'customer_id',
        'product_id',
        'invoice_number',
        'reason',
        'status',
        'item_condition',
        'refund_method',
        'refund_proof_path',
        'assigned_to',
        'chat_active',
        'tracking_token',
    ];

    protected function casts(): array
    {
        return [
            'status' => TicketStatus::class,
            'item_condition' => ItemCondition::class,
            'chat_active' => 'boolean',
        ];
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function evidences(): HasMany
    {
        return $this->hasMany(TicketEvidence::class);
    }

    public function communications(): HasMany
    {
        return $this->hasMany(TicketCommunication::class);
    }

    public function chatMessages(): HasMany
    {
        return $this->hasMany(ChatMessage::class);
    }

    public function statusHistories(): HasMany
    {
        return $this->hasMany(TicketStatusHistory::class);
    }

    /**
     * URL publik untuk pelacakan tiket oleh pelanggan.
     */
    public function trackingUrl(): string
    {
        return route('portal.tracking.show', ['ticket_number' => $this->ticket_number]);
    }
}
