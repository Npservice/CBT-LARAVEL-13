<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['nama_sekolah', 'akreditasi_sekolah', 'tingkat'])]
class Institusi extends Model
{
    use HasUuids;

    protected $table = 'institusi';

    public function kelas(): HasMany
    {
        return $this->hasMany(Kelas::class, 'institusi_id');
    }
}
