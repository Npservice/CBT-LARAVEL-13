<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('paket_soal', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('kelas_id')->nullable()->constrained('kelas');
            $table->string('kode_kelas')->nullable();
            $table->foreignUuid('mapel_id')->constrained('mata_pelajaran');
            $table->string('mata_pelajaran');
            $table->foreignUuid('created_by')->constrained('guru_pengampu');
            $table->string('dibuat_oleh')->nullable();
            $table->integer('durasi_menit');
            $table->string('kode_soal')->unique();
            $table->timestamps();
        });

        Schema::create('sesi_ujian', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('paket_soal_id')->constrained('paket_soal');
            $table->string('judul')->nullable();
            $table->foreignUuid('kelas_id')->nullable()->constrained('kelas');
            $table->string('kode_kelas')->nullable();
            $table->string('kode_paket');
            $table->integer('sesi');
            $table->dateTime('start');
            $table->dateTime('end');
            $table->timestamps();
        });

        Schema::create('soal_pilihan_ganda', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('paket_soal_id')->constrained('paket_soal');
            $table->text('pertanyaan');
            $table->integer('nilai');
            $table->timestamps();
        });

        Schema::create('pilihan', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('soal_pilihan_ganda_id')->constrained('soal_pilihan_ganda')->onDelete('cascade');
            $table->text('pilihan');
            $table->boolean('benar');
            $table->timestamps();
        });

        Schema::create('soal_essai', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('paket_soal_id')->constrained('paket_soal');
            $table->text('pertanyaan');
            $table->integer('nilai');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('soal_essai');
        Schema::dropIfExists('pilihan');
        Schema::dropIfExists('soal_pilihan_ganda');
        Schema::dropIfExists('sesi_ujian');
        Schema::dropIfExists('paket_soal');
        Schema::enableForeignKeyConstraints();
    }
};
