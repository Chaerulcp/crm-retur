<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/dashboard', function () {
    return redirect()->route('staff.dashboard');
})->middleware(['auth'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Modul domain (dikelola oleh masing-masing agen pengembang).
require __DIR__.'/portal.php';
require __DIR__.'/staff.php';
require __DIR__.'/admin.php';
require __DIR__.'/chat.php';

require __DIR__.'/auth.php';
