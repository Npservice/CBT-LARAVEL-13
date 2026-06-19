<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('mata_pelajaran_kelas')) {
            Schema::create('mata_pelajaran_kelas', function (Blueprint $table) {
                $table->foreignUuid('mata_pelajaran_id')->constrained('mata_pelajaran')->onDelete('cascade');
                $table->foreignUuid('kelas_id')->constrained('kelas')->onDelete('cascade');
                $table->primary(['mata_pelajaran_id', 'kelas_id']);
            });
        }

        if (!Schema::hasTable('mata_pelajaran_jurusan')) {
            Schema::create('mata_pelajaran_jurusan', function (Blueprint $table) {
                $table->foreignUuid('mata_pelajaran_id')->constrained('mata_pelajaran')->onDelete('cascade');
                $table->foreignUuid('jurusan_id')->constrained('jurusan')->onDelete('cascade');
                $table->primary(['mata_pelajaran_id', 'jurusan_id']);
            });
        }

        // Migrate existing single kelas/jurusan data to pivots
        if (Schema::hasColumn('mata_pelajaran', 'kelas_id')) {
            $mapels = DB::table('mata_pelajaran')->get(['id', 'kelas_id', 'jurusan_id']);
            foreach ($mapels as $mapel) {
                if ($mapel->kelas_id) {
                    DB::table('mata_pelajaran_kelas')->insertOrIgnore([
                        'mata_pelajaran_id' => $mapel->id,
                        'kelas_id'          => $mapel->kelas_id,
                    ]);
                }
                if (!empty($mapel->jurusan_id) && Schema::hasTable('mata_pelajaran_jurusan')) {
                    DB::table('mata_pelajaran_jurusan')->insertOrIgnore([
                        'mata_pelajaran_id' => $mapel->id,
                        'jurusan_id'        => $mapel->jurusan_id,
                    ]);
                }
            }

            Schema::table('mata_pelajaran', function (Blueprint $table) {
                $table->dropForeign(['kelas_id']);
                $table->dropForeign(['jurusan_id']);
                $table->dropColumn(['kelas_id', 'jurusan_id']);
            });
        }
    }

    public function down(): void
    {
        Schema::table('mata_pelajaran', function (Blueprint $table) {
            $table->foreignUuid('kelas_id')->nullable()->constrained('kelas');
            $table->foreignUuid('jurusan_id')->nullable()->constrained('jurusan');
        });

        Schema::dropIfExists('mata_pelajaran_jurusan');
        Schema::dropIfExists('mata_pelajaran_kelas');
    }
};
