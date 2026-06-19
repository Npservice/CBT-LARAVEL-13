<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('guru_pengampu', function (Blueprint $table) {
            $table->foreignUuid('kelas_id')->nullable()->constrained('kelas')->onDelete('cascade');
            $table->unique(['guru_id', 'mata_pelajaran_id', 'kelas_id'], 'gp_guru_mapel_kelas_unique');
        });
    }

    public function down(): void
    {
        Schema::table('guru_pengampu', function (Blueprint $table) {
            $table->dropUnique('gp_guru_mapel_kelas_unique');
            $table->dropForeign(['kelas_id']);
            $table->dropColumn('kelas_id');
        });
    }
};
