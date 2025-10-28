<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\GetpasController;
use App\Http\Controllers\ApprovalController;
use Illuminate\Support\Facades\Auth;

// 1. ROUTE PUBLIK (Hanya halaman utama)

Route::get('/', function () {
    // Jika User sudah login, arahkan ke dashboard
    if (Auth::check()) {
        return redirect()->route('dashboard');
    }
    // Jika belum login, arahkan ke halaman login
    return redirect()->route('login');
});

// 2. ROUTE AUTENTIKASI DARI BREEZE
require __DIR__.'/auth.php';

// 3. ROUTE TERPROTEKSI (Memerlukan Login: auth dan verifikasi email: verified)
// Kita gabungkan semua route yang memerlukan login ke dalam satu group ini.
Route::middleware(['auth', 'verified'])->group(function () {
    
    // a. DASHBOARD (Hanya SATU definisi yang memanggil controller kita)
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // b. PROFIL USER (Dari Breeze, pastikan ProfileController sudah diimpor)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // c. GETPAS WORKFLOW (Semua route aplikasi inti)
    Route::resource('getpas', GetpasController::class); // Mencakup getpas.create (GET /getpas/create)
    
    // d. ROUTE TAMBAHAN WORKFLOW
    Route::post('getpas/{getpas}/submit', [GetpasController::class, 'submitForApproval'])->name('getpas.submit');
    Route::post('/approval/{approval}/action', [ApprovalController::class, 'handleAction'])->name('approval.action');
    Route::get('getpas/{getpas}/print', [GetpasController::class, 'printFinal'])->name('getpas.print');
});