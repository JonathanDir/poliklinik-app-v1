
<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\ObatController;
use App\Http\Controllers\Admin\PoliController;
use App\Http\Controllers\Admin\DokterController;
use App\Http\Controllers\Dokter\JadwalPeriksaController;
use App\Http\Controllers\Pasien\PendaftaranController;
use App\Http\Controllers\Dokter\AntrianController;
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
            return view('admin.dashboard', [
                'jumlahDokter' => \App\Models\User::where('role', 'dokter')->count(),
                'jumlahPasien' => \App\Models\User::where('role', 'pasien')->count(),
                'jumlahPoli' => \App\Models\Poli::count(),
                'jumlahObat' => \App\Models\Obat::count(),
            ]);
        })->name('admin.dashboard');

        // CRUD Manajemen Obat
        Route::resource('obat', ObatController::class)->except(['show']);
        Route::resource('poli', PoliController::class)->except(['show']);
        Route::resource('dokter', DokterController::class)->except(['show']);
    });

// Dokter Routes
Route::middleware(['auth', 'role:dokter'])
    ->prefix('dokter')
    ->group(function () {

        Route::get('/dashboard', function () {
            return redirect()->route('dokter.antrian');
        })->name('dokter.dashboard');

        // CRUD Jadwal Periksa
        Route::resource(
            'jadwal-periksa',
            JadwalPeriksaController::class
        )->except(['show']);
        Route::get('/antrian', [AntrianController::class, 'index'])->name('dokter.antrian');
        Route::post('/antrian/{daftarPoli}/status', [AntrianController::class, 'status'])->name('dokter.antrian.status');
        Route::post('/antrian/{daftarPoli}/periksa', [AntrianController::class, 'periksa'])->name('dokter.antrian.periksa');
    });

// Pasien Routes
Route::middleware(['auth', 'role:pasien'])
    ->prefix('pasien')
    ->group(function () {

        Route::get('/dashboard', function () {
            return redirect()->route('pendaftaran.index');
        })->name('pasien.dashboard');
        Route::get('/pendaftaran', [PendaftaranController::class, 'index'])->name('pendaftaran.index');
        Route::post('/pendaftaran', [PendaftaranController::class, 'store'])->name('pendaftaran.store');
        Route::delete('/pendaftaran/{daftarPoli}', [PendaftaranController::class, 'destroy'])->name('pendaftaran.destroy');
    });
