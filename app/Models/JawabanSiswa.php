<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

#[Fillable(['sesi_ujian_id', 'koreksi_by', 'start', 'end', 'siswa_id', 'nama_siswa'])]
class JawabanSiswa extends Model
{
    use HasUuids;

    protected $table = 'jawaban_siswa';

    protected $casts = [
        'start' => 'datetime',
        'end' => 'datetime',
    ];

    public function sesiUjian(): BelongsTo
    {
        return $this->belongsTo(SesiUjian::class, 'sesi_ujian_id');
    }

    public function siswa(): BelongsTo
    {
        return $this->belongsTo(Siswa::class, 'siswa_id');
    }

    public function jawabPilihanGanda(): HasMany
    {
        return $this->hasMany(JawabPilihanGanda::class, 'jawaban_siswa_id');
    }

    public function jawabEssai(): HasMany
    {
        return $this->hasMany(JawabEssai::class, 'jawaban_siswa_id');
    }

    public function nilaiSiswa(): HasOne
    {
        return $this->hasOne(NilaiSiswa::class, 'jawaban_id');
    }
}
