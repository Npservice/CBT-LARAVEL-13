<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['kelas_id', 'kode_kelas', 'mapel_id', 'mata_pelajaran', 'created_by', 'dibuat_oleh', 'durasi_menit', 'kode_soal'])]
class PaketSoal extends Model
{
    use HasUuids;

    protected $table = 'paket_soal';

    public function pembuat(): BelongsTo
    {
        return $this->belongsTo(GuruPengampu::class, 'created_by');
    }

    public function soalPilihanGanda(): HasMany
    {
        return $this->hasMany(SoalPilihanGanda::class, 'paket_soal_id');
    }

    public function soalEssai(): HasMany
    {
        return $this->hasMany(SoalEssai::class, 'paket_soal_id');
    }

    public function sesiUjian(): HasMany
    {
        return $this->hasMany(SesiUjian::class, 'paket_soal_id');
    }
}
