<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['soal_pilihan_ganda_id', 'pilihan', 'benar'])]
class Pilihan extends Model
{
    use HasUuids;

    protected $table = 'pilihan';

    protected $casts = [
        'benar' => 'boolean',
    ];

    public function soalPilihanGanda(): BelongsTo
    {
        return $this->belongsTo(SoalPilihanGanda::class, 'soal_pilihan_ganda_id');
    }
}
