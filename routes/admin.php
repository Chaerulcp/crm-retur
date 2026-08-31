<?php

use App\Http\Controllers\Admin\AnalyticsController;
use App\Http\Controllers\Admin\FaqController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Modul Admin & Analitik
|--------------------------------------------------------------------------
| Kontrak route yang WAJIB ada (nama route -> URI):
| - admin.users.*     resource /admin/users     (role:Admin)
| - admin.products.*  resource /admin/products  (role:Admin)
| - admin.faqs.*      resource /admin/faqs      (role:Admin,Manajemen,Customer Service)
| - admin.analytics   GET /admin/analytics      (role:Admin,Manajemen)
|
| View: resources/views/admin/**, controller: App\Http\Controllers\Admin\*.
*/

Route::name('admin.')->prefix('admin')->middleware(['auth'])->group(function () {
    // Manajemen staf & produk khusus peran Admin.
    Route::resource('users', UserController::class)
        ->except('show')
        ->middleware('role:Admin');

    Route::resource('products', ProductController::class)
        ->except('show')
        ->middleware('role:Admin');

    // FAQ dapat dikelola Admin, Manajemen, dan Customer Service (paritas
    // dengan sistem lawas yang membuka menu FAQ untuk ketiga peran tersebut).
    Route::resource('faqs', FaqController::class)
        ->except('show')
        ->middleware('role:Admin,Manajemen,Customer Service');

    // Dasbor analitik untuk Admin dan Manajemen.
    Route::get('/analytics', AnalyticsController::class)
        ->middleware('role:Admin,Manajemen')
        ->name('analytics');
});
