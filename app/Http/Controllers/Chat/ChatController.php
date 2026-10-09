<?php

namespace App\Http\Controllers\Chat;

use App\Http\Controllers\Controller;
use App\Models\ChatMessage;
use App\Models\ReturnTicket;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

use App\Services\AiChatService;

class ChatController extends Controller
{
    private AiChatService $aiChatService;

    public function __construct(AiChatService $aiChatService)
    {
        $this->aiChatService = $aiChatService;
    }

    /**
     * Polling pesan baru (ascending) setelah ID tertentu.
     *
     * Respons: { data: [{id, sender_type, sender_name, message, created_at, is_ai_generated}], ticket: { chat_active } }
     */
    public function messages(Request $request, ReturnTicket $ticket): JsonResponse
    {
        if ($denied = $this->denyUnlessAllowed($request, $ticket)) {
            return $denied;
        }

        $messages = $ticket->chatMessages()
            ->where('id', '>', $request->integer('after_id'))
            ->orderBy('id')
            ->get()
            ->map(fn (ChatMessage $message): array => $this->formatMessage($message))
            ->values();

        return response()->json([
            'data' => $messages,
            'ticket' => ['chat_active' => (bool) $ticket->chat_active],
        ]);
    }

    /**
     * Simpan pesan baru dari staf (login) atau pelanggan (via token).
     */
    public function store(Request $request, ReturnTicket $ticket): JsonResponse
    {
        if ($denied = $this->denyUnlessAllowed($request, $ticket)) {
            return $denied;
        }

        $validated = $request->validate([
            'message' => ['required', 'string', 'max:2000'],
        ]);

        $user = $request->user();
        $isStaff = $user !== null;

        $message = $ticket->chatMessages()->create([
            'sender_type' => $isStaff ? ChatMessage::SENDER_STAFF : ChatMessage::SENDER_CUSTOMER,
            'sender_id' => $isStaff ? $user->id : null,
            'sender_name' => $isStaff ? $user->name : $ticket->customer?->name,
            'message' => $validated['message'],
        ]);

        // Auto-reply logic for customer message
        if (!$isStaff && env('AI_AUTO_REPLY_ENABLED', true)) {
            // Jalankan secara asynchronous atau sinkronous tergantung kebutuhan,
            // untuk kesederhanaan kita panggil sinkronous, tapi idealnya di-queue.
            $aiReply = $this->aiChatService->generateAutoReply($ticket, $validated['message']);
            
            if ($aiReply) {
                $ticket->chatMessages()->create([
                    'sender_type' => ChatMessage::SENDER_STAFF,
                    'sender_id' => null,
                    'sender_name' => '🤖 Asisten AI',
                    'message' => $aiReply,
                    'is_ai_generated' => true,
                ]);
            }
        }

        return response()->json(['data' => $this->formatMessage($message)], 201);
    }

    /**
     * Endpoint saran balasan dari AI untuk CS.
     */
    public function suggest(Request $request, ReturnTicket $ticket): JsonResponse
    {
        // Hanya staf yang boleh memanggil ini
        if ($request->user() === null) {
            return response()->json(['message' => 'Unauthorized.'], 403);
        }

        $suggestion = $this->aiChatService->suggestReply($ticket);
        
        return response()->json(['data' => $suggestion]);
    }

    /**
     * Resolusi akses per request:
     * - Staf login selalu boleh (termasuk melihat riwayat saat chat nonaktif).
     * - Selain itu wajib 'token' === tracking_token; pelanggan ditolak bila chat nonaktif.
     */
    private function denyUnlessAllowed(Request $request, ReturnTicket $ticket): ?JsonResponse
    {
        if ($request->user() !== null) {
            return null;
        }

        $token = (string) $request->input('token', '');

        if ($token === '' || ! hash_equals((string) $ticket->tracking_token, $token)) {
            return response()->json(['message' => 'Token akses tidak valid.'], 403);
        }

        if (! $ticket->chat_active) {
            return response()->json(['message' => 'Live chat untuk tiket ini sedang tidak aktif.'], 403);
        }

        return null;
    }

    /**
     * Bentuk payload pesan yang konsisten untuk messages & store.
     */
    private function formatMessage(ChatMessage $message): array
    {
        return [
            'id' => $message->id,
            'sender_type' => $message->sender_type,
            'sender_name' => $message->sender_name,
            'message' => $message->message,
            'created_at' => $message->created_at->format('d/m/Y H:i'),
            'is_ai_generated' => (bool) $message->is_ai_generated,
        ];
    }
}
