<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\VerifikasiController;
use App\Http\Controllers\SertifikatController;
use Illuminate\Support\Facades\Route;

// ─── AUTH ────────────────────────────────────────────────
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// ─── USER (authenticated) ─────────────────────────────────
Route::middleware('auth')->group(function () {
    Route::get('/sertifikat', [SertifikatController::class, 'index'])->name('sertifikat.index');
    Route::post('/sertifikat', [SertifikatController::class, 'store'])->name('sertifikat.store');
    Route::get('/sertifikat/{sertifikat}/edit', [SertifikatController::class, 'edit'])->name('sertifikat.edit');
    Route::put('/sertifikat/{sertifikat}', [SertifikatController::class, 'update'])->name('sertifikat.update');
    Route::delete('/sertifikat/{sertifikat}', [SertifikatController::class, 'destroy'])->name('sertifikat.destroy');
    Route::get('/sertifikat/{sertifikat}/generate', [SertifikatController::class, 'generate'])->name('sertifikat.generate');
    Route::get('/sertifikat/{sertifikat}/generate-pdf', [SertifikatController::class, 'generatePdf'])->name('sertifikat.generate-pdf');
    Route::get('/sertifikat/{sertifikat}/download', [SertifikatController::class, 'downloadPdf'])->name('sertifikat.download');
});

// ─── PUBLIC ROUTES ────────────────────────────────────────
Route::get('/', [VerifikasiController::class, 'index'])->name('verifikasi.index');
Route::post('/upload', [VerifikasiController::class, 'upload'])->name('verifikasi.upload');
Route::get('/sertifikat/{sertifikat}/verify', [SertifikatController::class, 'verify'])->name('sertifikat.verify');

// ─── ADMIN ONLY ───────────────────────────────────────────
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/logs', [AdminController::class, 'logs'])->name('logs');

    // User management
    Route::get('/users', [AdminController::class, 'users'])->name('users');
    Route::get('/users/create', [AdminController::class, 'createUser'])->name('users.create');
    Route::post('/users', [AdminController::class, 'storeUser'])->name('users.store');
    Route::get('/users/{user}/edit', [AdminController::class, 'editUser'])->name('users.edit');
    Route::put('/users/{user}', [AdminController::class, 'updateUser'])->name('users.update');
    Route::delete('/users/{user}', [AdminController::class, 'deleteUser'])->name('users.delete');
});
