<?php

namespace App\Services;

use App\Models\Faq;
use App\Models\ReturnTicket;
use App\Models\ChatMessage;
use Exception;

class AiChatService
{
    private ClaudeClient $claude;

    public function __construct(ClaudeClient $claude)
    {
        $this->claude = $claude;
    }

    /**
     * Generate auto-reply for customer when no staff is online or available.
     */
    public function generateAutoReply(ReturnTicket $ticket, string $customerMessage): ?string
    {
        if (!env('AI_AUTO_REPLY_ENABLED', true) || !env('AI_FEATURES_ENABLED', true)) {
            return null;
        }

        try {
            $systemPrompt = $this->buildSystemPrompt($ticket);
            
            // Format recent chat history (last 5 messages)
            $recentMessages = $ticket->chatMessages()
                ->orderBy('id', 'desc')
                ->take(5)
                ->get()
                ->reverse();

            $messages = [];
            foreach ($recentMessages as $msg) {
                if ($msg->message !== $customerMessage) {
                    $role = $msg->sender_type === ChatMessage::SENDER_STAFF ? 'assistant' : 'user';
                    $messages[] = [
                        'role' => $role,
                        'content' => $msg->message
                    ];
                }
            }

            // Add current customer message
            $messages[] = [
                'role' => 'user',
                'content' => $customerMessage
            ];

            $response = $this->claude->createMessage($systemPrompt, $messages, 500);

            return $response['content'][0]['text'] ?? null;
        } catch (Exception $e) {
            \Log::error('AiChatService auto-reply failed: ' . $e->getMessage());
            return null; // Silent fail, just don't reply
        }
    }

    /**
     * Generate suggested reply for staff based on the chat history.
     */
    public function suggestReply(ReturnTicket $ticket): ?string
    {
        if (!env('AI_FEATURES_ENABLED', true)) {
            return null;
        }

        try {
            $systemPrompt = $this->buildSystemPrompt($ticket) . "\n\n" .
                "TUGAS TAMBAHAN:\n" .
                "Sebagai asisten AI, buatkan draf balasan untuk dikirim oleh CS kepada pelanggan berdasarkan riwayat percakapan. Draf harus profesional, empatik, dan langsung menjawab keluhan terakhir pelanggan.";
            
            $recentMessages = $ticket->chatMessages()
                ->orderBy('id', 'asc')
                ->get();

            if ($recentMessages->isEmpty()) {
                return "Halo {$ticket->customer->name}, ada yang bisa kami bantu terkait retur tiket {$ticket->ticket_number}?";
            }

            $messages = [];
            foreach ($recentMessages as $msg) {
                $role = $msg->sender_type === ChatMessage::SENDER_STAFF ? 'assistant' : 'user';
                $messages[] = [
                    'role' => $role,
                    'content' => $msg->message
                ];
            }

            $response = $this->claude->createMessage($systemPrompt, $messages, 500);

            return $response['content'][0]['text'] ?? null;
        } catch (Exception $e) {
            \Log::error('AiChatService suggest reply failed: ' . $e->getMessage());
            return null;
        }
    }

    private function buildSystemPrompt(ReturnTicket $ticket): string
    {
        $appName = config('app.name');
        
        $faqs = Faq::where('is_active', true)->get()->map(function ($faq) {
            return "Q: {$faq->question}\nA: {$faq->answer}";
        })->implode("\n\n");

        return <<<PROMPT
Kamu adalah asisten layanan pelanggan AI untuk {$appName}. Kamu membantu pelanggan yang mengajukan retur/pengembalian barang.

KONTEKS TIKET SAAT INI:
- Nomor Tiket: {$ticket->ticket_number}
- Produk: {$ticket->product->name}
- Status Tiket: {$ticket->status->value}
- Alasan Retur: {$ticket->reason}

FAQ TERSEDIA:
{$faqs}

ATURAN PENTING:
1. Jawab dalam Bahasa Indonesia yang ramah, sopan, dan profesional.
2. Jika pertanyaan bisa dijawab dari FAQ, gunakan informasi tersebut.
3. Jika pertanyaan di luar kemampuanmu, katakan bahwa staf CS manusia kami akan segera membalas pesan ini saat jam kerja.
4. JANGAN membuat janji soal refund, penggantian barang, atau keputusan apapun karena itu wewenang staf Gudang dan Manajemen.
5. Fokus pada memberikan informasi status saat ini dan prosedur retur.
PROMPT;
    }
}
