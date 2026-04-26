<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Slider extends Model
{
    /**
     * @var list<string>
     */
    protected $fillable = [
        'sort_order',
        'is_active',
        'title',
        'badge',
        'title_highlight',
        'description',
        'subtitle',
        'image_1',
        'image_2',
        'link',
        'magicbox',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'magicbox' => 'array',
        ];
    }

    public function imageUrl(?string $path): ?string
    {
        if (! $path) {
            return null;
        }

        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }

        $path = ltrim($path, '/');
        if (str_starts_with($path, 'v1/')) {
            return asset($path);
        }

        return asset('storage/'.$path);
    }

    public function image1Url(): ?string
    {
        return $this->imageUrl($this->image_1);
    }

    public function image2Url(): ?string
    {
        return $this->imageUrl($this->image_2);
    }

    /** @param  \Illuminate\Database\Eloquent\Builder<static>  $query */
    public function scopeActiveOrdered($query)
    {
        return $query->where('is_active', true)->orderBy('sort_order')->orderBy('id');
    }
}
