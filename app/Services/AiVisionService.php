<?php

namespace App\Services;

use App\Models\ReturnTicket;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class AiVisionService
{
    protected ClaudeClient $claude;
    protected string $visionModel;

    public function __construct(ClaudeClient $claude)
    {
        $this->claude = $claude;
        $this->visionModel = config('services.anthropic.vision_model', 'claude-3-5-sonnet-20241022');
    }

    public function analyzeTicket(ReturnTicket $ticket): void
    {
        if (!config('services.anthropic.ai_features_enabled', true)) {
            return;
        }

        $images = $ticket->evidences()->where('kind', 'image')->get();
        if ($images->isEmpty()) {
            return;
        }

        $contentBlocks = [
            [
                'type' => 'text',
                'text' => "Alasan Retur Pelanggan: \"{$ticket->reason}\"\nNama Produk: \"{$ticket->product->name}\"\nMohon analisis foto-foto bukti berikut dan tentukan apakah alasan pelanggan sesuai dengan kondisi barang di foto. Berikan juga fraud score (0-100) di mana 100 berarti sangat mencurigakan (bukan cacat pabrik atau rusak saat pengiriman, melainkan disengaja atau berbeda produk)."
            ]
        ];

        foreach ($images as $image) {
            $path = Storage::path($image->path);
            if (file_exists($path)) {
                $imageData = base64_encode(file_get_contents($path));
                $mimeType = mime_content_type($path);
                
                // Ensure supported media types (jpeg, png, gif, webp)
                if (in_array($mimeType, ['image/jpeg', 'image/png', 'image/gif', 'image/webp'])) {
                    $contentBlocks[] = [
                        'type' => 'image',
                        'source' => [
                            'type' => 'base64',
                            'media_type' => $mimeType,
                            'data' => $imageData,
                        ]
                    ];
                }
            }
        }

        if (count($contentBlocks) === 1) {
            return; // No valid images found
        }

        $systemPrompt = "Kamu adalah agen asisten QC (Quality Control) Retur. Tugasmu adalah menganalisis kesesuaian antara retunly retur pelanggan dengan foto bukti yang mereka lampirkan. 
Berikan hasil analisismu dengan struktur JSON yang valid berisi 2 key: 
1. 'analysis': Penjelasan singkat (maksimal 3 kalimat) dalam bahasa Indonesia mengenai apakah kerusakan/masalah di foto mendukung alasan pelanggan. 
2. 'fraud_score': Angka integer 0-100. 0 berarti sangat valid/jujur, 100 berarti kemungkinan penipuan tinggi (barang berbeda, cacat tidak wajar).
Hanya kembalikan JSON, jangan ada teks tambahan.";

        try {
            $response = $this->claude->createMessage(
                $systemPrompt,
                [
                    [
                        'role' => 'user',
                        'content' => $contentBlocks
                    ]
                ],
                500,
                $this->visionModel
            );

            $responseText = $response['content'][0]['text'] ?? '{}';
            
            // Kadang Claude menambahkan blok ```json, kita bersihkan
            $responseText = preg_replace('/```json|```/', '', $responseText);
            $result = json_decode(trim($responseText), true);

            if ($result && isset($result['analysis'])) {
                $ticket->update([
                    'ai_analysis_result' => $result['analysis'],
                    'ai_fraud_score' => $result['fraud_score'] ?? null,
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Claude Vision API error: ' . $e->getMessage());
        }
    }
}
