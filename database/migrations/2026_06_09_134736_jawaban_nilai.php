<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('jawaban_siswa', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('sesi_ujian_id')->constrained('sesi_ujian');
            $table->foreignUuid('koreksi_by')->nullable()->constrained('guru_pengampu');
            $table->dateTime('start')->nullable();
            $table->dateTime('end')->nullable();
            $table->foreignUuid('siswa_id')->constrained('siswa');
            $table->string('nama_siswa');
            $table->timestamps();
        });

        Schema::create('jawab_pilihan_ganda', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('jawaban_siswa_id')->constrained('jawaban_siswa')->onDelete('cascade');
            $table->foreignUuid('soal_id')->constrained('soal_pilihan_ganda');
            $table->foreignUuid('pilihan_id')->nullable()->constrained('pilihan');
            $table->integer('nilai')->default(0);
            $table->boolean('hasil')->nullable();
            $table->timestamps();
        });

        Schema::create('jawab_essai', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('jawaban_siswa_id')->constrained('jawaban_siswa')->onDelete('cascade');
            $table->foreignUuid('soal_id')->constrained('soal_essai');
            $table->text('jawaban')->nullable();
            $table->integer('nilai')->default(0);
            $table->timestamps();
        });

        Schema::create('nilai_siswa', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('siswa_id')->constrained('siswa');
            $table->foreignUuid('jawaban_id')->constrained('jawaban_siswa')->onDelete('cascade');
            $table->integer('nilai');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('nilai_siswa');
        Schema::dropIfExists('jawab_essai');
        Schema::dropIfExists('jawab_pilihan_ganda');
        Schema::dropIfExists('jawaban_siswa');
        Schema::enableForeignKeyConstraints();
    }
};
