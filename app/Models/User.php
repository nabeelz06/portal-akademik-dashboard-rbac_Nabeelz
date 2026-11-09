<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory;
    use HasRoles;
    use Notifiable;

    /**
     * Peran yang boleh membuka panel admin.
     */
    public const PERAN_PANEL = ['admin', 'staf'];

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function bolehMasukPanel(): bool
    {
        return $this->hasAnyRole(self::PERAN_PANEL);
    }

    /**
     * Nama rute tujuan setelah login: admin dan staf ke dashboard, lainnya ke beranda.
     */
    public function rutePulang(): string
    {
        return $this->bolehMasukPanel() ? 'admin.dashboard' : 'home';
    }

    public function labelPeran(): string
    {
        return match (true) {
            $this->hasRole('admin') => 'Admin',
            $this->hasRole('staf') => 'Staf',
            default => 'Pengguna Biasa',
        };
    }

    /**
     * Keterangan di menu pengguna AdminLTE (config adminlte.usermenu_desc).
     */
    public function adminlte_desc(): string
    {
        return $this->labelPeran();
    }

    /**
     * Tautan profil di menu pengguna AdminLTE (config adminlte.usermenu_profile_url).
     */
    public function adminlte_profile_url(): string
    {
        return 'profile';
    }
}
