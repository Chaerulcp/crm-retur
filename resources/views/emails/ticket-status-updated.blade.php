<x-mail::message>
# Status Retur Diperbarui

Halo **{{ $ticket->customer->name }}**,

Status permintaan retur Anda dengan nomor **{{ $ticket->ticket_number }}** telah diperbarui menjadi **{{ $ticket->status->label() }}**.

{{ $description }}

@if (! empty($note))
<x-mail::panel>
**Catatan tim kami:** {{ $note }}
</x-mail::panel>
@endif

<x-mail::button :url="$trackingUrl" color="primary">
Lacak Retur Saya
</x-mail::button>

Salam,<br>
{{ config('app.name') }}
</x-mail::message>
