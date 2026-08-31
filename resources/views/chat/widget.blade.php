{{--
    Widget Live Chat (chat-agent).
    Variabel kontrak:
      $ticket       : App\Models\ReturnTicket
      $chatContext  : 'portal' | 'staff'

    Alpine.js: polling chat.messages tiap 4 detik (token dari atribut data widget;
    portal memakai tracking_token, staf tanpa token), kirim via fetch POST chat.store
    dengan CSRF dari meta tag, bubble kanan/kiri + badge pengirim + waktu, auto-scroll.
--}}
@php
    $isStaffContext = ($chatContext ?? 'portal') === 'staff';
@endphp

<div id="chat-widget"
     data-chat-context="{{ $isStaffContext ? 'staff' : 'portal' }}"
     data-chat-token="{{ $isStaffContext ? '' : $ticket->tracking_token }}"
     data-chat-active="{{ $ticket->chat_active ? '1' : '0' }}"
     data-messages-url="{{ route('chat.messages', $ticket) }}"
     data-store-url="{{ route('chat.store', $ticket) }}"
     x-data="chatWidget"
     class="flex h-96 flex-col overflow-hidden rounded-xl border border-slate-200 bg-white shadow-[0_1px_2px_rgba(16,24,40,0.05)]">

    {{-- Kepala widget; staf melihat indikator status chat --}}
    <div class="flex items-center justify-between border-b border-slate-200 bg-slate-50/70 px-4 py-2.5">
        <h3 class="text-sm font-semibold text-ink">Live Chat</h3>
        @if ($isStaffContext)
            <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-0.5 text-xs font-semibold"
                  :class="chatActive ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-500'">
                <span class="h-1.5 w-1.5 rounded-full" :class="chatActive ? 'bg-emerald-500' : 'bg-slate-400'"></span>
                <span x-text="chatActive ? 'Chat aktif' : 'Chat tidak aktif'"></span>
            </span>
        @endif
    </div>

    {{-- Daftar pesan (bubble kanan untuk pesan sendiri, kiri untuk lawan bicara) --}}
    <div x-ref="scroller" class="flex-1 space-y-3 overflow-y-auto bg-paper p-4">
        <template x-if="messages.length === 0">
            <p class="pt-8 text-center text-xs text-slate-400">Belum ada pesan. Mulai percakapan di sini.</p>
        </template>
        <template x-for="msg in messages" :key="msg.id">
            <div class="flex" :class="isOwn(msg) ? 'justify-end' : 'justify-start'">
                <div class="max-w-[80%] rounded-2xl px-3.5 py-2 text-sm shadow-sm"
                     :class="isOwn(msg) ? 'rounded-br-md bg-brand-600 text-white' : 'rounded-bl-md border border-slate-200 bg-white text-slate-800'">
                    <span class="mb-1 inline-block rounded px-1.5 py-0.5 text-[10px] font-bold uppercase tracking-wide"
                          :class="msg.sender_type === 'staff' ? 'bg-brand-100 text-brand-700' : 'bg-emerald-100 text-emerald-700'"
                          x-text="msg.sender_type === 'staff' ? 'Staf' : 'Pelanggan'"></span>
                    <p class="whitespace-pre-line break-words" x-text="msg.message"></p>
                    <p class="mt-1 text-right font-mono text-[10px]"
                       :class="isOwn(msg) ? 'text-brand-200' : 'text-slate-400'"
                       x-text="msg.created_at"></p>
                </div>
            </div>
        </template>
    </div>

    {{-- Area kirim pesan --}}
    <div class="border-t border-slate-200 bg-white p-3">
        <p x-show="error" x-cloak x-text="error" class="mb-2 text-xs font-medium text-red-600"></p>

        <template x-if="isPortal() && !chatActive">
            <p class="py-1 text-center text-xs text-slate-400">
                Chat sedang tidak aktif. Silakan tunggu staf mengaktifkan chat.
            </p>
        </template>

        <form class="flex gap-2" @submit.prevent="send">
            <input type="text"
                   x-model="draft"
                   :disabled="inputDisabled()"
                   maxlength="2000"
                   placeholder="Tulis pesan…"
                   class="input flex-1 disabled:cursor-not-allowed disabled:bg-slate-100 disabled:text-slate-400">
            <button type="submit"
                    :disabled="inputDisabled() || sending || draft.trim() === ''"
                    class="btn-primary px-4 py-2 text-xs disabled:cursor-not-allowed disabled:opacity-50">
                <span x-text="sending ? '…' : 'Kirim'"></span>
            </button>
        </form>
    </div>
</div>


<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('chatWidget', () => ({
        messages: [],
        afterId: 0,
        polling: null,
        chatActive: false,
        draft: '',
        sending: false,
        error: null,

        init() {
            this.chatActive = this.$el.dataset.chatActive === '1';
            this.refresh();
            this.polling = setInterval(() => this.refresh(), 4000);
        },

        isPortal() {
            return this.$el.dataset.chatContext === 'portal';
        },

        inputDisabled() {
            return this.isPortal() && !this.chatActive;
        },

        // Pesan milik "saya": staf di panel staf, pelanggan di portal.
        isOwn(msg) {
            return this.isPortal()
                ? msg.sender_type === 'customer'
                : msg.sender_type === 'staff';
        },

        async refresh() {
            const params = new URLSearchParams({ after_id: this.afterId });
            const token = this.$el.dataset.chatToken;
            if (token) {
                params.set('token', token);
            }

            try {
                const res = await fetch(this.$el.dataset.messagesUrl + '?' + params.toString(), {
                    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                });

                if (res.status === 403) {
                    // Pelanggan ditolak (token salah / chat dinonaktifkan staf).
                    this.chatActive = false;
                    return;
                }
                if (!res.ok) {
                    return;
                }

                const payload = await res.json();
                if (Array.isArray(payload.data) && payload.data.length > 0) {
                    this.messages.push(...payload.data);
                    this.afterId = payload.data[payload.data.length - 1].id;
                    this.$nextTick(() => this.scrollToBottom());
                }
                if (payload.ticket) {
                    this.chatActive = Boolean(payload.ticket.chat_active);
                }
            } catch {
                // Gangguan jaringan sementara: coba lagi pada polling berikutnya.
            }
        },

        async send() {
            const message = this.draft.trim();
            if (!message || this.sending || this.inputDisabled()) {
                return;
            }

            this.sending = true;
            this.error = null;

            try {
                const res = await fetch(this.$el.dataset.storeUrl, {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    body: JSON.stringify({ message }),
                });

                const payload = await res.json().catch(() => ({}));

                if (res.ok && payload.data) {
                    this.draft = '';
                    this.messages.push(payload.data);
                    this.afterId = Math.max(this.afterId, payload.data.id);
                    this.$nextTick(() => this.scrollToBottom());
                } else if (res.status === 403) {
                    this.chatActive = false;
                    this.error = payload.message ?? 'Chat untuk tiket ini sedang tidak aktif.';
                } else {
                    this.error = payload.message ?? 'Pesan gagal terkirim. Coba lagi.';
                }
            } catch {
                this.error = 'Pesan gagal terkirim. Periksa koneksi Anda.';
            } finally {
                this.sending = false;
            }
        },

        scrollToBottom() {
            const scroller = this.$refs.scroller;
            if (scroller) {
                scroller.scrollTop = scroller.scrollHeight;
            }
        },
    }));
});
</script>

