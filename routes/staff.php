<?php

use App\Http\Controllers\Staff\DashboardController;
use App\Http\Controllers\Staff\TicketController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Modul Staf (internal, wajib login)
|--------------------------------------------------------------------------
| Kontrak route yang WAJIB ada (nama route -> URI):
| - staff.dashboard          GET  /staff/dashboard  (semua peran)
| - staff.tickets.index      GET  /staff/tickets    (filter & pencarian)
| - staff.tickets.show       GET  /staff/tickets/{ticket}
| - staff.tickets.transition POST /staff/tickets/{ticket}/transition
| - staff.tickets.communicate POST /staff/tickets/{ticket}/communicate
| - staff.tickets.condition  POST /staff/tickets/{ticket}/condition (vonis gudang)
| - staff.tickets.refund     POST /staff/tickets/{ticket}/refund
| - staff.tickets.chat-toggle POST /staff/tickets/{ticket}/chat-toggle
|
| Aturan peran dipegang lewat middleware 'role:...' dan/atau Gate,
| serta WAJIB melalui App\Services\TicketWorkflow untuk ubah status.
| View: resources/views/staff/**, controller: App\Http\Controllers\Staff\*.
*/

Route::name('staff.')->prefix('staff')->middleware(['auth'])->group(function () {
    Route::get('/dashboard', DashboardController::class)->name('dashboard');

    Route::get('/tickets', [TicketController::class, 'index'])->name('tickets.index');
    Route::get('/tickets/{ticket}', [TicketController::class, 'show'])->name('tickets.show');

    Route::post('/tickets/{ticket}/transition', [TicketController::class, 'transition'])->name('tickets.transition');
    Route::post('/tickets/{ticket}/communicate', [TicketController::class, 'communicate'])->name('tickets.communicate');
    Route::post('/tickets/{ticket}/condition', [TicketController::class, 'condition'])->name('tickets.condition');
    Route::post('/tickets/{ticket}/chat-toggle', [TicketController::class, 'toggleChat'])->name('tickets.chat-toggle');
    Route::post('/tickets/{ticket}/copilot', [TicketController::class, 'copilot'])->name('tickets.copilot');

    // Penyelesaian refund hanya untuk peran Manajemen & Admin.
    Route::post('/tickets/{ticket}/refund', [TicketController::class, 'refund'])
        ->middleware('role:Manajemen,Admin')
        ->name('tickets.refund');
});
