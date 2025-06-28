<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\SiswaController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return redirect()->route('login');
});

// Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// Admin Routes
Route::prefix('admin')->middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('absen.dashboard');
    Route::get('/pending-users', [AdminController::class, 'pendingUsers'])->name('absen.pending.users');
    Route::post('/approve-user/{id}', [AdminController::class, 'approveUser'])->name('absen.approve.user');
    Route::post('/reject-user/{id}', [AdminController::class, 'rejectUser'])->name('absen.reject.user');
    Route::get('/generate-qr', [AdminController::class, 'generateQrCode'])->name('absen.generate.qr');
    Route::get('/siswa', [AdminController::class, 'siswaIndex'])->name('absen.siswa.index');
    Route::put('/siswa/update-status/{id}', [AdminController::class, 'updateSiswaStatus'])->name('absen.siswa.update-status');
    Route::get('/absensi', [AdminController::class, 'absensiIndex'])->name('absen.absensi.index');
    Route::get('/scan-history', [AdminController::class, 'scanHistory'])->name('absen.scan.history');
    Route::get('/view-image/{id}/{type}', [AdminController::class, 'viewImage'])->name('absen.view.image');
    Route::get('/qrcode', [AdminController::class, 'qrcodeIndex'])->name('absen.qrcode.index');
    Route::get('/export-absensi', [AdminController::class, 'exportAbsensi'])->name('absen.export');
});

// Student Routes
Route::prefix('siswa')->middleware(['auth', 'role:siswa'])->group(function () {
    Route::get('/dashboard', [SiswaController::class, 'dashboard'])->name('siswa.dashboard');
    Route::get('/scan-qr', [SiswaController::class, 'scanQR'])->name('siswa.scan.qr');
    Route::post('/process-attendance', [SiswaController::class, 'processAttendance'])->name('siswa.process.attendance');
    Route::get('/attendance-history', [SiswaController::class, 'attendanceHistory'])->name('siswa.attendance.history');
    Route::get('/profile', [SiswaController::class, 'profile'])->name('siswa.profile');
    Route::post('/update-profile', [SiswaController::class, 'updateProfile'])->name('siswa.profile.update');
});