<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['paket_soal_id', 'pertanyaan', 'nilai'])]
class SoalPilihanGanda extends Model
{
    use HasUuids;

    protected $table = 'soal_pilihan_ganda';

    public function paketSoal(): BelongsTo
    {
        return $this->belongsTo(PaketSoal::class, 'paket_soal_id');
    }

    public function pilihan(): HasMany
    {
        return $this->hasMany(Pilihan::class, 'soal_pilihan_ganda_id');
    }
}
