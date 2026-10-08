<?php

use App\Http\Controllers\Portal\HomeController;
use App\Http\Controllers\Portal\SubmissionController;
use App\Http\Controllers\Portal\TrackingController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Modul Portal Pelanggan (publik, tanpa login)
|--------------------------------------------------------------------------
| Kontrak route yang WAJIB ada (nama route -> URI):
| - portal.home           GET  /                (landing + CTA)
| - portal.create         GET  /ajukan-retur    (form pengajuan)
| - portal.store          POST /ajukan-retur    (simpan pengajuan)
| - portal.success        GET  /retur-berhasil  (halaman sukses)
| - portal.tracking       GET  /lacak           (form pencarian tiket)
| - portal.tracking.show  GET  /lacak/{ticket_number} (status tiket)
| - portal.faq            GET  /faq
|
| Catatan: view disimpan di resources/views/portal/**,
| controller di App\Http\Controllers\Portal\*.
*/

Route::name('portal.')->group(function () {
    Route::get('/', [HomeController::class, 'home'])->name('home');
    Route::get('/faq', [HomeController::class, 'faq'])->name('faq');

    Route::get('/ajukan-retur', [SubmissionController::class, 'create'])->name('create');
    Route::post('/ajukan-retur', [SubmissionController::class, 'store'])->name('store');
    Route::get('/retur-berhasil', [SubmissionController::class, 'success'])->name('success');

    Route::get('/lacak', [TrackingController::class, 'index'])->name('tracking');
    Route::get('/lacak/{ticket_number}', [TrackingController::class, 'show'])
        ->where('ticket_number', '[A-Za-z0-9\-]+')
        ->name('tracking.show');
});
