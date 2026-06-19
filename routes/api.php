<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\SiswaController;
use App\Http\Controllers\Api\GuruController;
use App\Http\Controllers\Api\KelasController;
use App\Http\Controllers\Api\JurusanController;
use App\Http\Controllers\Api\InstitusiController;
use App\Http\Controllers\Api\MataPelajaranController;
use App\Http\Controllers\Api\GuruPengampuController;
use App\Http\Controllers\Api\PaketSoalController;
use App\Http\Controllers\Api\SesiUjianController;
use App\Http\Controllers\Api\SoalPilihanGandaController;
use App\Http\Controllers\Api\PilihanController;
use App\Http\Controllers\Api\SoalEssaiController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\SiswaPortalController;
use App\Http\Controllers\Api\HasilController;
use App\Http\Controllers\Api\RolePermissionController;

Route::prefix('v1')->group(function () {

    // ============================================
    // PUBLIC ROUTES
    // ============================================
    Route::post('/auth/login', [AuthController::class, 'login']);
    Route::post('/auth/register', [AuthController::class, 'register']);

    // ============================================
    // PROTECTED ROUTES
    // ============================================
    Route::middleware('auth:sanctum')->group(function () {

        // Auth
        Route::post('/auth/logout', [AuthController::class, 'logout']);
        Route::get('/auth/me',      [AuthController::class, 'me']);
        Route::post('/auth/refresh',[AuthController::class, 'refresh']);

        // Dashboard
        Route::get('/dashboard/stats',   [DashboardController::class, 'stats']);
        Route::post('/dashboard/refresh',[DashboardController::class, 'refresh']);

        // Role & Permission Management
        Route::get('/roles',                       [RolePermissionController::class, 'roles'])->middleware('permission:manage-roles');
        Route::get('/permissions',                 [RolePermissionController::class, 'permissions'])->middleware('permission:manage-roles');
        Route::put('/roles/{id}/permissions',      [RolePermissionController::class, 'syncPermissions'])->middleware('permission:manage-roles');

        // Users
        Route::get('/users',               [UserController::class, 'index'])->middleware('permission:manage-users');
        Route::post('/users',              [UserController::class, 'store'])->middleware('permission:manage-users');
        Route::get('/users/search/query',  [UserController::class, 'search'])->middleware('permission:manage-users');
        Route::get('/users/{id}',          [UserController::class, 'show'])->middleware('permission:manage-users');
        Route::put('/users/{id}',          [UserController::class, 'update'])->middleware('permission:manage-users');
        Route::delete('/users/{id}',       [UserController::class, 'destroy'])->middleware('permission:manage-users');

        // Siswa
        Route::get('/siswa',               [SiswaController::class, 'index'])->middleware('permission:view-siswa');
        Route::post('/siswa',              [SiswaController::class, 'store'])->middleware('permission:create-siswa');
        Route::get('/siswa/search/query',  [SiswaController::class, 'search'])->middleware('permission:view-siswa');
        Route::get('/siswa/{id}',          [SiswaController::class, 'show'])->middleware('permission:view-siswa');
        Route::put('/siswa/{id}',          [SiswaController::class, 'update'])->middleware('permission:edit-siswa');
        Route::delete('/siswa/{id}',       [SiswaController::class, 'destroy'])->middleware('permission:delete-siswa');

        // Guru
        Route::get('/guru',                [GuruController::class, 'index'])->middleware('permission:view-guru');
        Route::post('/guru',               [GuruController::class, 'store'])->middleware('permission:create-guru');
        Route::get('/guru/search/query',   [GuruController::class, 'search'])->middleware('permission:view-guru');
        Route::get('/guru/{id}',           [GuruController::class, 'show'])->middleware('permission:view-guru');
        Route::put('/guru/{id}',           [GuruController::class, 'update'])->middleware('permission:edit-guru');
        Route::delete('/guru/{id}',        [GuruController::class, 'destroy'])->middleware('permission:delete-guru');

        // Guru Pengampu
        // GET routes terbuka untuk semua authenticated (dropdown + guru akses data sendiri)
        Route::get('/guru-pengampu/saya',  [GuruPengampuController::class, 'saya']);
        Route::get('/guru-pengampu',       [GuruPengampuController::class, 'index']);
        Route::get('/guru-pengampu/{id}',  [GuruPengampuController::class, 'show']);
        Route::post('/guru-pengampu',                    [GuruPengampuController::class, 'store'])->middleware('permission:create-guru-pengampu');
        Route::put('/guru-pengampu/set-pembuat-soal',    [GuruPengampuController::class, 'setPembuatSoal'])->middleware('permission:edit-guru-pengampu');
        Route::put('/guru-pengampu/{id}',                [GuruPengampuController::class, 'update'])->middleware('permission:edit-guru-pengampu');
        Route::delete('/guru-pengampu/{id}',             [GuruPengampuController::class, 'destroy'])->middleware('permission:delete-guru-pengampu');

        // Institusi
        // GET terbuka (dropdown)
        Route::get('/institusi',           [InstitusiController::class, 'index']);
        Route::get('/institusi/{id}',      [InstitusiController::class, 'show']);
        Route::post('/institusi',          [InstitusiController::class, 'store'])->middleware('permission:create-institusi');
        Route::put('/institusi/{id}',      [InstitusiController::class, 'update'])->middleware('permission:edit-institusi');
        Route::delete('/institusi/{id}',   [InstitusiController::class, 'destroy'])->middleware('permission:delete-institusi');

        // Kelas
        Route::get('/kelas',               [KelasController::class, 'index']);
        Route::get('/kelas/{id}',          [KelasController::class, 'show']);
        Route::post('/kelas',              [KelasController::class, 'store'])->middleware('permission:create-kelas');
        Route::put('/kelas/{id}',          [KelasController::class, 'update'])->middleware('permission:edit-kelas');
        Route::delete('/kelas/{id}',       [KelasController::class, 'destroy'])->middleware('permission:delete-kelas');

        // Jurusan
        Route::get('/jurusan',             [JurusanController::class, 'index']);
        Route::get('/jurusan/{id}',        [JurusanController::class, 'show']);
        Route::post('/jurusan',            [JurusanController::class, 'store'])->middleware('permission:create-jurusan');
        Route::put('/jurusan/{id}',        [JurusanController::class, 'update'])->middleware('permission:edit-jurusan');
        Route::delete('/jurusan/{id}',     [JurusanController::class, 'destroy'])->middleware('permission:delete-jurusan');

        // Mata Pelajaran
        Route::get('/mata-pelajaran',      [MataPelajaranController::class, 'index']);
        Route::get('/mata-pelajaran/{id}', [MataPelajaranController::class, 'show']);
        Route::post('/mata-pelajaran',     [MataPelajaranController::class, 'store'])->middleware('permission:create-mata-pelajaran');
        Route::put('/mata-pelajaran/{id}', [MataPelajaranController::class, 'update'])->middleware('permission:edit-mata-pelajaran');
        Route::delete('/mata-pelajaran/{id}',[MataPelajaranController::class, 'destroy'])->middleware('permission:delete-mata-pelajaran');

        // Paket Soal
        Route::get('/paket-soal',          [PaketSoalController::class, 'index'])->middleware('permission:view-paket-soal');
        Route::post('/paket-soal',         [PaketSoalController::class, 'store'])->middleware('permission:create-paket-soal');
        Route::get('/paket-soal/{id}',     [PaketSoalController::class, 'show'])->middleware('permission:view-paket-soal');
        Route::put('/paket-soal/{id}',     [PaketSoalController::class, 'update'])->middleware('permission:edit-paket-soal');
        Route::delete('/paket-soal/{id}',  [PaketSoalController::class, 'destroy'])->middleware('permission:delete-paket-soal');

        // Sesi Ujian
        Route::get('/sesi-ujian',          [SesiUjianController::class, 'index'])->middleware('permission:view-sesi-ujian');
        Route::post('/sesi-ujian',         [SesiUjianController::class, 'store'])->middleware('permission:create-sesi-ujian');
        Route::get('/sesi-ujian/{id}',     [SesiUjianController::class, 'show'])->middleware('permission:view-sesi-ujian');
        Route::put('/sesi-ujian/{id}',     [SesiUjianController::class, 'update'])->middleware('permission:edit-sesi-ujian');
        Route::delete('/sesi-ujian/{id}',  [SesiUjianController::class, 'destroy'])->middleware('permission:delete-sesi-ujian');

        // Soal Pilihan Ganda
        Route::get('/soal-pilihan-ganda',         [SoalPilihanGandaController::class, 'index'])->middleware('permission:view-soal');
        Route::post('/soal-pilihan-ganda',         [SoalPilihanGandaController::class, 'store'])->middleware('permission:create-soal');
        Route::get('/soal-pilihan-ganda/{id}',     [SoalPilihanGandaController::class, 'show'])->middleware('permission:view-soal');
        Route::put('/soal-pilihan-ganda/{id}',     [SoalPilihanGandaController::class, 'update'])->middleware('permission:edit-soal');
        Route::delete('/soal-pilihan-ganda/{id}',  [SoalPilihanGandaController::class, 'destroy'])->middleware('permission:delete-soal');

        // Pilihan (opsi jawaban PG)
        Route::get('/pilihan',             [PilihanController::class, 'index'])->middleware('permission:view-soal');
        Route::post('/pilihan',            [PilihanController::class, 'store'])->middleware('permission:create-soal');
        Route::get('/pilihan/{id}',        [PilihanController::class, 'show'])->middleware('permission:view-soal');
        Route::put('/pilihan/{id}',        [PilihanController::class, 'update'])->middleware('permission:edit-soal');
        Route::delete('/pilihan/{id}',     [PilihanController::class, 'destroy'])->middleware('permission:delete-soal');

        // Soal Essai
        Route::get('/soal-essai',          [SoalEssaiController::class, 'index'])->middleware('permission:view-soal');
        Route::post('/soal-essai',         [SoalEssaiController::class, 'store'])->middleware('permission:create-soal');
        Route::get('/soal-essai/{id}',     [SoalEssaiController::class, 'show'])->middleware('permission:view-soal');
        Route::put('/soal-essai/{id}',     [SoalEssaiController::class, 'update'])->middleware('permission:edit-soal');
        Route::delete('/soal-essai/{id}',  [SoalEssaiController::class, 'destroy'])->middleware('permission:delete-soal');

        // Siswa Portal (Ujian) — no permission middleware, role check done in controller
        Route::get('/ujian/tersedia',                         [SiswaPortalController::class, 'tersedia']);
        Route::post('/ujian/{sesi_id}/mulai',                 [SiswaPortalController::class, 'mulai']);
        Route::get('/ujian/{jawaban_id}/soal',                [SiswaPortalController::class, 'getSoal']);
        Route::post('/ujian/{jawaban_id}/jawab-pg',           [SiswaPortalController::class, 'jawabPg']);
        Route::post('/ujian/{jawaban_id}/jawab-essai',        [SiswaPortalController::class, 'jawabEssai']);
        Route::post('/ujian/{jawaban_id}/selesai',            [SiswaPortalController::class, 'selesai']);

        // Hasil Ujian
        Route::get('/hasil',                                           [HasilController::class, 'index'])->middleware('permission:view-hasil');
        Route::get('/hasil/jawaban/{jawaban_id}',                      [HasilController::class, 'detail'])->middleware('permission:view-hasil');
        Route::post('/hasil/jawaban/{jawab_essai_id}/nilai-essai',     [HasilController::class, 'nilaiEssai'])->middleware('permission:nilai-essai');
        Route::get('/hasil/{kelas_id}/mapel',                          [HasilController::class, 'mapel'])->middleware('permission:view-hasil');
        Route::get('/hasil/{kelas_id}/{mapel_id}/siswa',               [HasilController::class, 'siswa'])->middleware('permission:view-hasil');

    });

});
