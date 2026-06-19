<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
// 1. Tabel Institusi
        Schema::create('institusi', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('nama_sekolah');
            $table->string('akreditasi_sekolah')->nullable();
            $table->enum('tingkat', ['SD', 'SMP', 'SMA', 'SMK']);
            $table->timestamps();
        });

// 2. Tabel Kelas
        Schema::create('kelas', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('kelas', 100);
            $table->string('kode_kelas')->unique();
            $table->timestamps();
        });

// 3. Tabel Jurusan
        Schema::create('jurusan', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('kode_jurusan');
            $table->string('nama_jurusan');
            $table->timestamps();
        });

// 4. Tabel Mata Pelajaran (kelas_id & jurusan_id dropped by refactor migration later)
        Schema::create('mata_pelajaran', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('kelas_id')->nullable()->constrained('kelas');
            $table->foreignUuid('jurusan_id')->nullable()->constrained('jurusan');
            $table->string('kode_mapel')->unique();
            $table->string('mapel');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('mata_pelajaran');
        Schema::dropIfExists('jurusan');
        Schema::dropIfExists('kelas');
        Schema::dropIfExists('institusi');
        Schema::enableForeignKeyConstraints();
    }
};
