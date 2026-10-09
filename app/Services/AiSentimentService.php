<?php

namespace App\Services;

use App\Models\ReturnTicket;
use Illuminate\Support\Facades\Log;

class AiSentimentService
{
    protected ClaudeClient $claude;

    public function __construct(ClaudeClient $claude)
    {
        $this->claude = $claude;
    }

    public function analyzeAndDraft(ReturnTicket $ticket): ?array
    {
        if (!config('services.anthropic.ai_features_enabled', true)) {
            return null;
        }

        // Kumpulkan riwayat percakapan dan catatan
        $communications = $ticket->communications()->oldest()->get();
        $chats = $ticket->chatMessages()->oldest()->get();

        $transcript = "ALASAN RETUR AWAL: " . $ticket->reason . "\n\n";

        if ($communications->isNotEmpty()) {
            $transcript .= "--- CATATAN & KOMUNIKASI SISTEM ---\n";
            foreach ($communications as $comm) {
                $sender = $comm->sender ? $comm->sender->name : 'Sistem/Pelanggan';
                $transcript .= "[{$sender}]: {$comm->message}\n";
            }
        }

        if ($chats->isNotEmpty()) {
            $transcript .= "\n--- RIWAYAT LIVE CHAT ---\n";
            foreach ($chats as $chat) {
                $role = $chat->is_from_agent ? ($chat->is_ai_generated ? 'AI Assistant' : 'CS Agent') : 'Pelanggan';
                $transcript .= "[{$role}]: {$chat->message}\n";
            }
        }

        $systemPrompt = "Kamu adalah CS Copilot untuk aplikasi retur Retunly. 
Tugasmu membaca transkrip tiket retur dan menghasilkan 3 hal dalam format JSON:
1. 'summary': Ringkasan singkat (maks 2 kalimat) tentang inti masalah tiket saat ini.
2. 'sentiment': Sentimen pelanggan saat ini. Pilih salah satu persis: 'Marah', 'Kecewa', 'Netral', 'Puas'.
3. 'draft_reply': Draf balasan (bahasa Indonesia yang sopan, empati, dan solutif) yang bisa langsung dikirim oleh staf CS kepada pelanggan untuk menanggapi situasi terakhir.

Hanya kembalikan JSON yang valid, tanpa teks tambahan di sekitarnya.";

        try {
            $response = $this->claude->createMessage(
                $systemPrompt,
                [
                    [
                        'role' => 'user',
                        'content' => "Berikut adalah transkrip tiketnya:\n\n" . $transcript
                    ]
                ],
                800
            );

            $responseText = $response['content'][0]['text'] ?? '{}';
            $responseText = preg_replace('/```json|```/', '', $responseText);
            $result = json_decode(trim($responseText), true);

            if ($result) {
                // Simpan ringkasan dan sentimen ke tiket
                $ticket->update([
                    'ai_summary' => $result['summary'] ?? null,
                    'ai_sentiment' => $result['sentiment'] ?? null,
                ]);

                return $result;
            }
        } catch (\Exception $e) {
            Log::error('Claude Sentiment API error: ' . $e->getMessage());
        }

        return null;
    }
}
