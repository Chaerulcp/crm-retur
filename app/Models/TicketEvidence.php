<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class TicketEvidence extends Model
{
    use HasFactory;

    /**
     * Nama tabel basis data.
     *
     * Ditentukan eksplisit karena Laravel menganggap kata "evidence" sebagai
     * uncountable, sehingga inferensi otomatis menghasilkan "ticket_evidence",
     * padahal migration membuat tabel "ticket_evidences".
     */
    protected $table = 'ticket_evidences';

    protected $fillable = [
        'return_ticket_id',
        'path',
        'original_name',
        'kind',
        'size',
    ];

    public function returnTicket(): BelongsTo
    {
        return $this->belongsTo(ReturnTicket::class);
    }

    public function url(): string
    {
        return Storage::url($this->path);
    }

    public function isImage(): bool
    {
        return $this->kind === 'image';
    }

    public function isVideo(): bool
    {
        return $this->kind === 'video';
    }
}
