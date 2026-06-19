<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\GuruPengampu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GuruPengampuController extends Controller
{
    private function withRelations()
    {
        return ['guru', 'mataPelajaran', 'kelas.jurusan'];
    }

    public function index(Request $request)
    {
        $user = $request->user();
        $isGuruRole = in_array($user->role, ['guru', 'guru-pembuat-soal']);

        $baseScope = function($q) use ($isGuruRole, $user) {
            if ($isGuruRole) {
                $q->whereHas('guru', fn($gq) => $gq->where('user_id', $user->id));
            }
        };

        $totalOriginal = GuruPengampu::tap($baseScope)->count();
        $query = GuruPengampu::with($this->withRelations())->tap($baseScope);

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->whereHas('guru', fn($gq) => $gq->where('nama', 'like', "%{$search}%"))
                  ->orWhereHas('mataPelajaran', fn($mq) => $mq->where('mapel', 'like', "%{$search}%"));
            });
        }

        $perPage = $request->get('per_page', 10);
        $page    = $request->get('page', 1);
        $result  = $query->orderBy('id')->paginate($perPage, ['*'], 'page', $page);

        return response()->json([
            'data' => $result->items(),
            'pagination' => [
                'total'          => $result->total(),
                'total_original' => $totalOriginal,
                'per_page'       => (int) $perPage,
                'current_page'   => (int) $result->currentPage(),
                'last_page'      => (int) $result->lastPage(),
                'from'           => $result->firstItem() ?: 0,
                'to'             => $result->lastItem() ?: 0,
            ]
        ]);
    }

    public function show($id)
    {
        return response()->json([
            'data' => GuruPengampu::with($this->withRelations())->findOrFail($id)
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'guru_id'           => 'required|exists:guru,id',
            'mata_pelajaran_id' => 'required|exists:mata_pelajaran,id',
            'kelas_id'          => 'required|exists:kelas,id',
            'pembuat_soal'      => 'nullable|boolean',
        ]);

        // Pastikan kelas ini memang terdaftar untuk mapel tersebut
        $kelasValid = DB::table('mata_pelajaran_kelas')
            ->where('mata_pelajaran_id', $request->mata_pelajaran_id)
            ->where('kelas_id', $request->kelas_id)
            ->exists();

        if (!$kelasValid) {
            return response()->json(['message' => 'Kelas tidak terdaftar untuk mata pelajaran ini'], 422);
        }

        // Cek apakah slot (mapel, kelas) sudah diisi guru lain
        $existing = GuruPengampu::with('guru')
            ->where('mata_pelajaran_id', $request->mata_pelajaran_id)
            ->where('kelas_id', $request->kelas_id)
            ->first();

        if ($existing) {
            $namaGuru = $existing->guru?->nama ?? 'guru lain';
            return response()->json([
                'message' => "Kelas ini sudah dipegang oleh {$namaGuru} untuk mapel yang sama. Hapus assignment tersebut terlebih dahulu."
            ], 422);
        }

        // PS berlaku per mata_pelajaran_id (global, bukan per kelas)
        // Jika set sebagai PS: unset semua guru lain untuk mapel ini, lalu set semua record guru ini untuk mapel ini
        if ($request->boolean('pembuat_soal')) {
            GuruPengampu::where('mata_pelajaran_id', $request->mata_pelajaran_id)
                ->update(['pembuat_soal' => false]);

            // Setelah unset semua, set semua record guru ini untuk mapel ini jadi true
            GuruPengampu::where('guru_id', $request->guru_id)
                ->where('mata_pelajaran_id', $request->mata_pelajaran_id)
                ->update(['pembuat_soal' => true]);
        }

        $record = GuruPengampu::create([
            'guru_id'           => $request->guru_id,
            'mata_pelajaran_id' => $request->mata_pelajaran_id,
            'kelas_id'          => $request->kelas_id,
            'pembuat_soal'      => $request->boolean('pembuat_soal'),
        ]);

        return response()->json([
            'message' => 'Guru Pengampu berhasil ditambahkan',
            'data'    => $record->load($this->withRelations()),
        ], 201);
    }

    /**
     * Toggle pembuat soal per (guru, mata_pelajaran) — berlaku ke semua kelas yang diajar guru tersebut untuk mapel itu.
     * Hanya satu guru yang boleh jadi PS per mata_pelajaran_id.
     */
    public function setPembuatSoal(Request $request)
    {
        $request->validate([
            'guru_id'           => 'required|exists:guru,id',
            'mata_pelajaran_id' => 'required|exists:mata_pelajaran,id',
            'pembuat_soal'      => 'required|boolean',
        ]);

        $guruId  = $request->guru_id;
        $mapelId = $request->mata_pelajaran_id;

        // Pastikan guru ini memang mengajar mapel ini
        $exists = GuruPengampu::where('guru_id', $guruId)
            ->where('mata_pelajaran_id', $mapelId)
            ->exists();

        if (!$exists) {
            return response()->json(['message' => 'Guru tidak mengajar mata pelajaran ini'], 422);
        }

        if ($request->boolean('pembuat_soal')) {
            // Unset semua guru untuk mapel ini
            GuruPengampu::where('mata_pelajaran_id', $mapelId)
                ->update(['pembuat_soal' => false]);
            // Set semua record guru ini untuk mapel ini jadi PS
            GuruPengampu::where('guru_id', $guruId)
                ->where('mata_pelajaran_id', $mapelId)
                ->update(['pembuat_soal' => true]);
        } else {
            // Cabut PS dari semua record guru ini untuk mapel ini
            GuruPengampu::where('guru_id', $guruId)
                ->where('mata_pelajaran_id', $mapelId)
                ->update(['pembuat_soal' => false]);
        }

        return response()->json(['message' => 'Pembuat soal berhasil diperbarui']);
    }

    public function update(Request $request, $id)
    {
        $record = GuruPengampu::findOrFail($id);

        $request->validate([
            'pembuat_soal' => 'required|boolean',
        ]);

        // Delegasikan ke logika PS global
        $this->setPembuatSoal(new Request([
            'guru_id'           => $record->guru_id,
            'mata_pelajaran_id' => $record->mata_pelajaran_id,
            'pembuat_soal'      => $request->boolean('pembuat_soal'),
        ]));

        return response()->json([
            'message' => 'Guru Pengampu berhasil diperbarui',
            'data'    => $record->fresh($this->withRelations()),
        ]);
    }

    public function saya(Request $request)
    {
        $user = $request->user();
        $records = GuruPengampu::with(['mataPelajaran', 'kelas.jurusan'])
            ->whereHas('guru', fn($q) => $q->where('user_id', $user->id))
            ->get()
            ->map(fn($gp) => [
                'id'                => $gp->id,
                'mata_pelajaran_id' => $gp->mata_pelajaran_id,
                'mapel'             => $gp->mataPelajaran?->mapel,
                'kode_mapel'        => $gp->mataPelajaran?->kode_mapel,
                'kelas_id'          => $gp->kelas_id,
                'kelas'             => $gp->kelas ? [
                    'id'         => $gp->kelas->id,
                    'kelas'      => $gp->kelas->kelas,
                    'kode_kelas' => $gp->kelas->kode_kelas,
                    'jurusan'    => $gp->kelas->jurusan?->nama_jurusan,
                ] : null,
                'pembuat_soal'      => (bool) $gp->pembuat_soal,
            ]);

        return response()->json(['data' => $records]);
    }

    public function destroy($id)
    {
        GuruPengampu::findOrFail($id)->delete();
        return response()->json(['message' => 'Guru Pengampu berhasil dihapus']);
    }
}
