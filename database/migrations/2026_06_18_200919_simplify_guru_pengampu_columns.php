<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('guru_pengampu', function (Blueprint $table) {
            if (Schema::hasColumn('guru_pengampu', 'kelas_id')) {
                $table->dropForeign(['kelas_id']);
                $table->dropColumn('kelas_id');
            }
            if (Schema::hasColumn('guru_pengampu', 'kode_kelas')) {
                $table->dropColumn('kode_kelas');
            }
            if (Schema::hasColumn('guru_pengampu', 'nama_guru')) {
                $table->dropColumn('nama_guru');
            }
            if (Schema::hasColumn('guru_pengampu', 'kode_mapel')) {
                $table->dropColumn('kode_mapel');
            }
        });
    }

    public function down(): void
    {
        Schema::table('guru_pengampu', function (Blueprint $table) {
            $table->foreignUuid('kelas_id')->nullable()->constrained('kelas')->nullOnDelete();
            $table->string('kode_kelas')->nullable();
            $table->string('nama_guru')->nullable();
            $table->string('kode_mapel')->nullable();
        });
    }
};
