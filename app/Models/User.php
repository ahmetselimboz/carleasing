<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    public const ROLE_ADMIN = 'admin';

    /** Müşteri Hizmetleri (destek / operasyon) */
    public const ROLE_CUSTOMER_SERVICE = 'customer_service';

    /**
     * @return array<string, string>
     */
    public static function assignableRoles(): array
    {
        return [
            self::ROLE_ADMIN => 'Admin',
            self::ROLE_CUSTOMER_SERVICE => 'Müşteri Hizmetleri',
        ];
    }

    /**
     * @return array<string, string>
     */
    public static function roleLabels(): array
    {
        return [
            self::ROLE_ADMIN => 'Admin',
            self::ROLE_CUSTOMER_SERVICE => 'Müşteri Hizmetleri',
        ] + [
            // veritabanında eski değer kalırsa
            'user' => 'Kullanıcı',
        ];
    }

    public function roleLabel(): string
    {
        if ($this->is_super_admin) {
            return 'Süper Admin';
        }

        return self::roleLabels()[$this->role] ?? ucfirst(str_replace('_', ' ', $this->role));
    }

    /**
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'image',
        'active',
        'role',
    ];

    /**
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'active' => 'boolean',
            'is_super_admin' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::deleting(function (User $user): bool {
            if ($user->is_super_admin) {
                return false;
            }

            return true;
        });
    }

    /** Panelde ve listelerde gösterilecek ad (Süper Admin gizlenir). */
    public function displayName(): string
    {
        if ($this->is_super_admin) {
            return 'Yönetici';
        }

        return $this->name;
    }

    /** Kullanıcı listelerinde süper admin satırını hariç tut. */
    public function scopeWithoutSuperAdmins(Builder $query): Builder
    {
        return $query->where('is_super_admin', false);
    }
}
