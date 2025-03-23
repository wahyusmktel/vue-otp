<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\Auth\OTPVerificationController;
use App\Http\Controllers\DataTanahController;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/verify-otp/{phone}', [OTPVerificationController::class, 'show'])->name('verification.show');
Route::post('/verify-otp', [OTPVerificationController::class, 'verify'])->name('verification.verify');
Route::post('/resend-otp', [OTPVerificationController::class, 'resend'])->name('verification.resend');

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'otp.verified'])->name('dashboard');

// Group dengan middleware auth dan otp
Route::middleware(['auth', 'otp.verified'])->group(function () {
    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Data Tanah - manual route
    Route::get('/data-tanah', [DataTanahController::class, 'index'])->name('data-tanah.index');
    Route::get('/data-tanah/create', [DataTanahController::class, 'create'])->name('data-tanah.create');
    Route::post('/data-tanah', [DataTanahController::class, 'store'])->name('data-tanah.store');
    Route::get('/data-tanah/{data_tanah}', [DataTanahController::class, 'show'])->name('data-tanah.show');
    Route::get('/data-tanah/{data_tanah}/edit', [DataTanahController::class, 'edit'])->name('data-tanah.edit');

    // 🛠️ Custom route update pakai POST + method override PUT (karena pakai FormData)
    Route::post('/data-tanah/{data_tanah}', [DataTanahController::class, 'update'])->name('data-tanah.update');

    // Hapus data
    Route::delete('/data-tanah/{data_tanah}', [DataTanahController::class, 'destroy'])->name('data-tanah.destroy');

    Route::get('/data-tanah/export/excel', [DataTanahController::class, 'exportExcel'])->name('data-tanah.export.excel');
    Route::get('/data-tanah/export/pdf', [DataTanahController::class, 'exportPDF'])->name('data-tanah.export.pdf');

});

require __DIR__.'/auth.php';
