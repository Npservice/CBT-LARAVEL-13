<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Pastikan ada standalone index pada guru_id sebelum drop composite unique
        // (MySQL memakai composite unique itu sebagai pendukung FK guru_id)
        $existingIndexes = collect(DB::select("SHOW INDEX FROM guru_pengampu"))->pluck('Key_name')->unique();
        if (!$existingIndexes->contains('gp_guru_id_idx')) {
            Schema::table('guru_pengampu', fn($t) => $t->index('guru_id', 'gp_guru_id_idx'));
        }

        // Bersihkan duplikat (mapel, kelas) yang mungkin ada di data lama.
        // Untuk setiap (mapel, kelas) yang punya >1 guru: pertahankan yang pembuat_soal=true,
        // kalau tidak ada yang PS, pertahankan yang paling baru (created_at terbesar).
        $dupes = DB::table('guru_pengampu')
            ->selectRaw('mata_pelajaran_id, kelas_id, COUNT(*) as total')
            ->groupBy('mata_pelajaran_id', 'kelas_id')
            ->having('total', '>', 1)
            ->get();

        foreach ($dupes as $dupe) {
            $rows = DB::table('guru_pengampu')
                ->where('mata_pelajaran_id', $dupe->mata_pelajaran_id)
                ->where('kelas_id', $dupe->kelas_id)
                ->orderByDesc('pembuat_soal')
                ->orderByDesc('created_at')
                ->get();

            // Pertahankan baris pertama, hapus sisanya
            $keep = $rows->first();
            $deleteIds = $rows->slice(1)->pluck('id');
            if ($deleteIds->isNotEmpty()) {
                DB::table('guru_pengampu')->whereIn('id', $deleteIds)->delete();
            }
        }

        // Refresh index list setelah kemungkinan partial run sebelumnya
        $existingIndexes = collect(DB::select("SHOW INDEX FROM guru_pengampu"))->pluck('Key_name')->unique();

        Schema::table('guru_pengampu', function (Blueprint $table) use ($existingIndexes) {
            if ($existingIndexes->contains('gp_guru_mapel_kelas_unique')) {
                $table->dropUnique('gp_guru_mapel_kelas_unique');
            }
            if (!$existingIndexes->contains('gp_mapel_kelas_unique')) {
                $table->unique(['mata_pelajaran_id', 'kelas_id'], 'gp_mapel_kelas_unique');
            }
        });
    }

    public function down(): void
    {
        Schema::table('guru_pengampu', function (Blueprint $table) {
            $table->dropUnique('gp_mapel_kelas_unique');
            $table->unique(['guru_id', 'mata_pelajaran_id', 'kelas_id'], 'gp_guru_mapel_kelas_unique');
        });

        Schema::table('guru_pengampu', function (Blueprint $table) {
            $table->dropIndex('gp_guru_id_idx');
        });
    }
};
