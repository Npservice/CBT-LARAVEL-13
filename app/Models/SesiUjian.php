<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['paket_soal_id', 'judul', 'kelas_id', 'kode_kelas', 'kode_paket', 'sesi', 'start', 'end'])]
class SesiUjian extends Model
{
    use HasUuids;

    protected $table = 'sesi_ujian';

    protected $casts = [
        'start' => 'datetime',
        'end' => 'datetime',
    ];

    public function paketSoal(): BelongsTo
    {
        return $this->belongsTo(PaketSoal::class, 'paket_soal_id');
    }

    public function kelas(): BelongsTo
    {
        return $this->belongsTo(Kelas::class, 'kelas_id');
    }

    public function jawabanSiswa(): HasMany
    {
        return $this->hasMany(JawabanSiswa::class, 'sesi_ujian_id');
    }
}
