<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasColumn('sesi_ujian', 'judul')) {
            Schema::table('sesi_ujian', function (Blueprint $table) {
                $table->string('judul')->nullable()->after('paket_soal_id');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('sesi_ujian', 'judul')) {
            Schema::table('sesi_ujian', function (Blueprint $table) {
                $table->dropColumn('judul');
            });
        }
    }
};
