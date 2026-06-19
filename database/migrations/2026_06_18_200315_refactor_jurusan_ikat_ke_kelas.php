<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('kelas', 'jurusan_id')) {
            Schema::table('kelas', function (Blueprint $table) {
                $table->foreignUuid('jurusan_id')->nullable()->after('kode_kelas')
                    ->constrained('jurusan')->nullOnDelete();
            });
        }

        Schema::dropIfExists('mata_pelajaran_jurusan');

        Schema::table('guru_pengampu', function (Blueprint $table) {
            if (Schema::hasColumn('guru_pengampu', 'jurusan_id')) {
                $table->dropForeign(['jurusan_id']);
                $table->dropColumn('jurusan_id');
            }
            if (Schema::hasColumn('guru_pengampu', 'nama_jurusan')) {
                $table->dropColumn('nama_jurusan');
            }
        });

        Schema::table('paket_soal', function (Blueprint $table) {
            if (Schema::hasColumn('paket_soal', 'jurusan_id')) {
                $table->dropForeign(['jurusan_id']);
                $table->dropColumn('jurusan_id');
            }
            if (Schema::hasColumn('paket_soal', 'nama_jurusan')) {
                $table->dropColumn('nama_jurusan');
            }
        });

        Schema::table('sesi_ujian', function (Blueprint $table) {
            if (Schema::hasColumn('sesi_ujian', 'jurusan_id')) {
                $table->dropForeign(['jurusan_id']);
                $table->dropColumn('jurusan_id');
            }
            if (Schema::hasColumn('sesi_ujian', 'nama_jurusan')) {
                $table->dropColumn('nama_jurusan');
            }
        });
    }

    public function down(): void
    {
        Schema::table('kelas', function (Blueprint $table) {
            $table->dropForeign(['jurusan_id']);
            $table->dropColumn('jurusan_id');
        });

        Schema::create('mata_pelajaran_jurusan', function (Blueprint $table) {
            $table->foreignUuid('mata_pelajaran_id')->constrained('mata_pelajaran')->cascadeOnDelete();
            $table->foreignUuid('jurusan_id')->constrained('jurusan')->cascadeOnDelete();
            $table->primary(['mata_pelajaran_id', 'jurusan_id']);
        });

        Schema::table('guru_pengampu', function (Blueprint $table) {
            $table->foreignUuid('jurusan_id')->nullable()->constrained('jurusan')->nullOnDelete();
            $table->string('nama_jurusan')->nullable();
        });

        Schema::table('paket_soal', function (Blueprint $table) {
            $table->foreignUuid('jurusan_id')->nullable()->constrained('jurusan')->nullOnDelete();
            $table->string('nama_jurusan')->nullable();
        });

        Schema::table('sesi_ujian', function (Blueprint $table) {
            $table->foreignUuid('jurusan_id')->nullable()->constrained('jurusan')->nullOnDelete();
            $table->string('nama_jurusan')->nullable();
        });
    }
};
