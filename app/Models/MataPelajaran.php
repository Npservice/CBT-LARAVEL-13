<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

#[Fillable(['kode_mapel', 'mapel'])]
class MataPelajaran extends Model
{
    use HasUuids;

    protected $table = 'mata_pelajaran';

    public function kelas(): BelongsToMany
    {
        return $this->belongsToMany(Kelas::class, 'mata_pelajaran_kelas', 'mata_pelajaran_id', 'kelas_id');
    }

}
