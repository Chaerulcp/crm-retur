@props(['status'])

{{-- Badge status tiket: warna mengikuti TicketStatus::badgeClass(). --}}
<span {{ $attributes->merge(['class' => 'inline-flex items-center gap-1.5 whitespace-nowrap rounded-full px-2.5 py-1 text-xs font-semibold ' . $status->badgeClass()]) }}>
    <span aria-hidden="true" class="h-1.5 w-1.5 rounded-full bg-current opacity-70"></span>
    {{ $status->label() }}
</span>