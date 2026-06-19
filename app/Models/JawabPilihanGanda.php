<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['jawaban_siswa_id', 'soal_id', 'pilihan_id', 'nilai', 'hasil'])]
class JawabPilihanGanda extends Model
{
    use HasUuids;

    protected $table = 'jawab_pilihan_ganda';

    protected $casts = [
        'hasil' => 'boolean',
    ];

    public function jawabanSiswa(): BelongsTo
    {
        return $this->belongsTo(JawabanSiswa::class, 'jawaban_siswa_id');
    }

    public function soal(): BelongsTo
    {
        return $this->belongsTo(SoalPilihanGanda::class, 'soal_id');
    }

    public function pilihan(): BelongsTo
    {
        return $this->belongsTo(Pilihan::class, 'pilihan_id');
    }
}
