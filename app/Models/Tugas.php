<?php

namespace App\Models;

use Database\Factories\TugasFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Tugas extends Model
{
    /** @use HasFactory<TugasFactory> */
    use HasFactory;

    protected $table = 'tugas';

    protected $fillable = ['judul', 'deskripsi', 'departemen_id', 'dosen_id', 'tenggat'];

    protected function casts(): array
    {
        return [
            'tenggat' => 'date',
        ];
    }

    /**
     * Tugas aktif = tenggatnya belum terlewati (hari ini masih dihitung).
     */
    public function scopeAktif(Builder $query): Builder
    {
        return $query->whereDate('tenggat', '>=', today());
    }

    public function departemen(): BelongsTo
    {
        return $this->belongsTo(Departemen::class);
    }

    public function dosen(): BelongsTo
    {
        return $this->belongsTo(Dosen::class);
    }
}
