<x-mail::message>
# Permohonan Maaf, Retur Belum Dapat Disetujui

Halo **{{ $ticket->customer->name }}**,

Mohon maaf, permintaan retur Anda dengan nomor **{{ $ticket->ticket_number }}** belum dapat kami setujui.

@if (! empty($note))
<x-mail::panel>
**Alasan penolakan:** {{ $note }}
</x-mail::panel>
@endif

Jika ada pertanyaan, silakan hubungi layanan pelanggan kami dengan menyebutkan nomor retur Anda.

Salam,<br>
{{ config('app.name') }}
</x-mail::message>
