<?php

use Illuminate\Support\Facades\Route;

// ============================================
// PUBLIC ROUTES
// ============================================
Route::redirect('/', '/login')->name('home');

// ============================================
// GUEST ROUTES
// ============================================
Route::middleware('guest.token')->group(function () {
    Route::get('/login', fn() => view('auth.login'))->name('login');
});

// ============================================
// PROTECTED ROUTES
// ============================================
Route::middleware('auth.token')->group(function () {

    // ============================================
    // ADMIN ROUTES (admin, guru, guru-pembuat-soal)
    // ============================================
    Route::prefix('admin')->middleware('role:admin,guru,guru-pembuat-soal')->group(function () {

        // Dashboard — semua role admin group bisa akses
        Route::get('/', fn() => view('pages.admin.dashboard'))->name('admin.dashboard');

        // User Management — admin only
        Route::get('/users', fn() => view('pages.admin.users'))->name('admin.users')
            ->middleware('web.permission:manage-users');

        // Role & Permission — admin only
        Route::get('/roles', fn() => view('pages.admin.roles'))->name('admin.roles')
            ->middleware('web.permission:manage-roles');
        Route::get('/permissions', fn() => view('pages.admin.permissions'))->name('admin.permissions')
            ->middleware('web.permission:manage-roles');

        // Siswa — admin only
        Route::get('/siswa', fn() => view('pages.admin.siswa'))->name('admin.siswa')
            ->middleware('web.permission:view-siswa');

        // Guru — admin only
        Route::get('/guru', fn() => view('pages.admin.guru'))->name('admin.guru')
            ->middleware('web.permission:view-guru');

        // Guru Pengampu — admin + guru roles (guru: lihat data sendiri)
        Route::get('/guru-pengampu', fn() => view('pages.admin.guru-pengampu'))->name('admin.guru-pengampu')
            ->middleware('web.permission:view-guru-pengampu');

        // Master Data — admin only
        Route::get('/institusi', fn() => view('pages.admin.institusi'))->name('admin.institusi')
            ->middleware('web.permission:view-institusi');
        Route::get('/kelas', fn() => view('pages.admin.kelas'))->name('admin.kelas')
            ->middleware('web.permission:view-kelas');
        Route::get('/jurusan', fn() => view('pages.admin.jurusan'))->name('admin.jurusan')
            ->middleware('web.permission:view-jurusan');
        Route::get('/mata-pelajaran', fn() => view('pages.admin.mata-pelajaran'))->name('admin.mata-pelajaran')
            ->middleware('web.permission:view-mata-pelajaran');

        // Paket Soal — guru & guru-pembuat-soal (view), guru-pembuat-soal (crud)
        Route::get('/paket-soal', fn() => view('pages.admin.paket-soal'))->name('admin.paket-soal')
            ->middleware('web.permission:view-paket-soal');
        Route::get('/paket-soal/{id}/soal', fn($id) => view('pages.admin.soal', ['paketId' => $id]))->name('admin.paket-soal.soal')
            ->middleware('web.permission:view-soal');

        // Sesi Ujian — guru & guru-pembuat-soal
        Route::get('/sesi-ujian', fn() => view('pages.admin.sesi-ujian'))->name('admin.sesi-ujian')
            ->middleware('web.permission:view-sesi-ujian');

        // Hasil Ujian
        Route::get('/hasil', fn() => view('pages.admin.hasil.index'))->name('admin.hasil')
            ->middleware('web.permission:view-hasil');
        Route::get('/hasil/jawaban/{jawaban_id}', fn($jawabanId) => view('pages.admin.hasil.detail', ['jawabanId' => $jawabanId]))->name('admin.hasil.detail')
            ->middleware('web.permission:view-hasil');
        Route::get('/hasil/{kelas_id}', fn($kelasId) => view('pages.admin.hasil.mapel', ['kelasId' => $kelasId]))->name('admin.hasil.mapel')
            ->middleware('web.permission:view-hasil');
        Route::get('/hasil/{kelas_id}/{mapel_id}', fn($kelasId, $mapelId) => view('pages.admin.hasil.siswa', ['kelasId' => $kelasId, 'mapelId' => $mapelId]))->name('admin.hasil.siswa')
            ->middleware('web.permission:view-hasil');
    });

    // ============================================
    // SISWA ROUTES
    // ============================================
    Route::prefix('siswa')->middleware('role:siswa')->group(function () {
        Route::get('/', fn() => view('pages.siswa.dashboard'))->name('siswa.dashboard');
        Route::get('/ujian/{jawaban_id}', fn($jawabanId) => view('pages.siswa.ujian', ['jawabanId' => $jawabanId]))->name('siswa.ujian');
    });

});
