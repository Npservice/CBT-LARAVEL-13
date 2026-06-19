<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['user_id', 'nig', 'nama', 'jenis_kelamin', 'tempat_lahir', 'tanggal_lahir', 'email', 'no_hp', 'alamat', 'is_aktif'])]
class Guru extends Model
{
    use HasUuids;

    protected $table = 'guru';

    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function guruPengampu(): HasMany
    {
        return $this->hasMany(GuruPengampu::class, 'guru_id');
    }
}
