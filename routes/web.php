<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

require __DIR__.'/auth.php';

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::get('/clear', function() {
    Artisan::call('view:clear');
    Artisan::call('config:clear');
    return "Cache dibersihkan!";
});

Route::get('/buat-admin', function () {
    $email = "edp.plsda@gmail.com";
    $user = User::updateOrCreate(
        ['email' => $email], // Ganti dengan email Anda
        [
            'name' => 'Admin Utama',
            'password' => Hash::make('Pangles1234'), // Ganti passwordnya
        ]
    );
    return "{$email} sudah berhasil jadi jadi Admi!";
});

Route::get('/update-user', function () {
    // Cari user berdasarkan email
    $user = User::where('email', 'husni.m@sekar.co.id')->first();

    if ($user) {
        $user->update([
            'password' => Hash::make('Pangles1234'), // Password baru
            'name'     => 'Husni',        // Opsional kalau mau ganti nama
        ]);
        return "Password user " . $user->email . " berhasil di-reset!";
    }

    return "User tidak ditemukan, Cak! Cek maneh email-e.";
});

Route::get('/fix-autoload', function() {
    // Karena kita tidak bisa dump-autoload, kita paksa hapus cache services
    $files = [
        base_path('bootstrap/cache/packages.php'),
        base_path('bootstrap/cache/services.php'),
        base_path('bootstrap/cache/config.php')
    ];

    foreach ($files as $file) {
        if (file_exists($file)) unlink($file);
    }

    return "Peta folder diperbarui! Silakan refresh halaman Admin.";
});

Route::get('/reset-kabeh', function () {
    // 1. Hapus file cache bootstrap secara manual
    $files = [
        base_path('bootstrap/cache/services.php'),
        base_path('bootstrap/cache/packages.php'),
        base_path('bootstrap/cache/config.php'),
        base_path('bootstrap/cache/routes-v7.php'),
    ];

    foreach ($files as $file) {
        if (File::exists($file)) {
            File::delete($file);
        }
    }

    // 2. Bersihkan folder cache filament
    $filamentCachePath = base_path('bootstrap/cache/filament/panels');
    if (File::isDirectory($filamentCachePath)) {
        File::cleanDirectory($filamentCachePath);
    }

    // 3. Jalankan perintah internal Laravel untuk bersihkan sisa-sisa
    Artisan::call('view:clear');
    Artisan::call('cache:clear');

    return "Ritual pembersihan selesai! Semua cache 'ingatan lama' sudah dibuang. <br> Silakan coba akses kembali halaman <a href='/admin'>Admin</a>.";
});