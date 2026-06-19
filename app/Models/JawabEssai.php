<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['jawaban_siswa_id', 'soal_id', 'jawaban', 'nilai'])]
class JawabEssai extends Model
{
    use HasUuids;

    protected $table = 'jawab_essai';

    public function jawabanSiswa(): BelongsTo
    {
        return $this->belongsTo(JawabanSiswa::class, 'jawaban_siswa_id');
    }

    public function soal(): BelongsTo
    {
        return $this->belongsTo(SoalEssai::class, 'soal_id');
    }
}
