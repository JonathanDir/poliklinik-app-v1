
<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\ObatController;
use App\Http\Controllers\Dokter\JadwalPeriksaController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

// Authentication
Route::get('/login', [AuthController::class, 'showLogin'])
    ->name('login');

Route::post('/login', [AuthController::class, 'login']);

Route::get('/register', [AuthController::class, 'showRegister']);

Route::post('/register', [AuthController::class, 'register'])
    ->name('register');

Route::post('/logout', [AuthController::class, 'logout'])
    ->name('logout');

// Admin Routes
Route::middleware(['auth', 'role:admin'])
    ->prefix('admin')
    ->group(function () {

        Route::get('/dashboard', function () {
            return view('admin.dashboard');
        })->name('admin.dashboard');

        // CRUD Manajemen Obat
        Route::resource('obat', ObatController::class);
    });

// Dokter Routes
Route::middleware(['auth', 'role:dokter'])
    ->prefix('dokter')
    ->group(function () {

        Route::get('/dashboard', function () {
            return view('dokter.dashboard');
        })->name('dokter.dashboard');

        // CRUD Jadwal Periksa
        Route::resource(
            'jadwal-periksa',
            JadwalPeriksaController::class
        );
    });

// Pasien Routes
Route::middleware(['auth', 'role:pasien'])
    ->prefix('pasien')
    ->group(function () {

        Route::get('/dashboard', function () {
            return view('pasien.dashboard');
        })->name('pasien.dashboard');
    });
