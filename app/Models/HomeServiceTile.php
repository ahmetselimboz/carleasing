<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HomeServiceTile extends Model
{
    /**
     * @var list<string>
     */
    protected $fillable = [
        'sort_order',
        'is_active',
        'image',
        'icon',
        'title',
        'description',
        'link_url',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function imageUrl(): ?string
    {
        if (! $this->image) {
            return null;
        }

        if (str_starts_with($this->image, 'http://') || str_starts_with($this->image, 'https://')) {
            return $this->image;
        }

        $path = ltrim($this->image, '/');
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
