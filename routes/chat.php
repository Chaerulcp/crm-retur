<?php

use App\Http\Controllers\Chat\ChatController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Modul Live Chat
|--------------------------------------------------------------------------
| Kontrak route yang WAJIB ada (nama route -> URI):
| - chat.messages  GET  /chat/{ticket}/messages?after_id=  (polling pesan baru)
| - chat.store     POST /chat/{ticket}/message             (kirim pesan)
|
| Aturan akses (divalidasi di ChatController per request):
| - Staf: wajib login (auth).
| - Pelanggan: tanpa login, tetapi WAJIB menyertakan 'token' yang cocok
|   dengan kolom tracking_token tiket, dan chat tiket harus aktif.
|
| Controller: App\Http\Controllers\Chat\ChatController (JSON endpoint).
*/

Route::name('chat.')->prefix('chat')->group(function () {
    Route::get('/ping', fn () => response()->json(['ok' => true]))->name('ping');
    Route::get('/{ticket}/messages', [ChatController::class, 'messages'])->name('messages');
    Route::post('/{ticket}/message', [ChatController::class, 'store'])->name('store');
});

