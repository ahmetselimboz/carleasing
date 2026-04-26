<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HomeTestimonial extends Model
{
    /**
     * @var list<string>
     */
    protected $fillable = [
        'sort_order',
        'is_active',
        'name',
        'role',
        'avatar',
        'quote',
        'rating',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'rating' => 'integer',
        ];
    }

    public function avatarUrl(): ?string
    {
        if (! $this->avatar) {
            return null;
        }

        if (str_starts_with($this->avatar, 'http://') || str_starts_with($this->avatar, 'https://')) {
            return $this->avatar;
        }

        $path = ltrim($this->avatar, '/');
        if (str_starts_with($path, 'v1/')) {
            return asset($path);
        }

        return asset('storage/'.$path);
    }

    /** @param  \Illuminate\Database\Eloquent\Builder<static>  $query */
    public function scopeActiveOrdered($query)
    {
        return $query->where('is_active', true)->orderBy('sort_order')->orderBy('id');
    }
}
