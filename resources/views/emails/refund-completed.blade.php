<x-mail::message>
# Proses Retur Selesai

Halo **{{ $ticket->customer->name }}**,

Proses retur Anda dengan nomor **{{ $ticket->ticket_number }}** telah **selesai**.

@if ($ticket->refund_method)
Pengembalian dana diselesaikan melalui **{{ $ticket->refund_method }}**.
@endif

Terima kasih atas kesabaran dan kerja sama Anda selama proses ini.

<x-mail::button :url="$trackingUrl" color="success">
Lihat Detail Retur
</x-mail::button>

Salam,<br>
{{ config('app.name') }}
</x-mail::message>
