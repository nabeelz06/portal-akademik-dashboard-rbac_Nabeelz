<?php

namespace App\Models;

use Database\Factories\DosenFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Dosen extends Model
{
    /** @use HasFactory<DosenFactory> */
    use HasFactory;

    /**
     * Pilihan jabatan fungsional dosen.
     */
    public const JABATAN = ['Asisten Ahli', 'Lektor', 'Lektor Kepala', 'Profesor'];

    protected $table = 'dosen';

    protected $fillable = ['nip', 'nama', 'email', 'departemen_id', 'jabatan'];

    public function departemen(): BelongsTo
    {
        return $this->belongsTo(Departemen::class);
    }

    public function mahasiswaWali(): HasMany
    {
        return $this->hasMany(Mahasiswa::class, 'dosen_wali_id');
    }

    public function tugas(): HasMany
    {
        return $this->hasMany(Tugas::class);
    }
}
