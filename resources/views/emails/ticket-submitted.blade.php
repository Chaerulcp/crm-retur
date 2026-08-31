<x-mail::message>
# Pengajuan Retur Diterima

Halo **{{ $ticket->customer->name }}**,

Terima kasih telah mengajukan retur. Permintaan Anda sudah kami terima dan sedang menunggu pemeriksaan tim Customer Service.

<x-mail::panel>
**Nomor Retur:** {{ $ticket->ticket_number }}<br>
**Produk:** {{ $ticket->product->name }}<br>
**Status:** {{ $ticket->status->label() }}
</x-mail::panel>

Simpan nomor retur ini untuk melacak proses retur Anda kapan saja.

<x-mail::button :url="$trackingUrl" color="primary">
Lacak Retur Saya
</x-mail::button>

Kami akan memberi tahu Anda melalui email setiap kali status retur berubah.

Salam,<br>
{{ config('app.name') }}
</x-mail::message>
