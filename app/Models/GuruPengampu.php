<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Kelas;

#[Fillable(['guru_id', 'mata_pelajaran_id', 'kelas_id', 'pembuat_soal'])]
class GuruPengampu extends Model
{
    use HasUuids;

    protected $table = 'guru_pengampu';

    public function guru(): BelongsTo
    {
        return $this->belongsTo(Guru::class, 'guru_id');
    }

    public function mataPelajaran(): BelongsTo
    {
        return $this->belongsTo(MataPelajaran::class, 'mata_pelajaran_id');
    }

    public function kelas(): BelongsTo
    {
        return $this->belongsTo(Kelas::class, 'kelas_id');
    }

    public function paketSoal(): HasMany
    {
        return $this->hasMany(PaketSoal::class, 'created_by');
    }
}
