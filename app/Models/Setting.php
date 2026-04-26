<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    public const VIEW_CACHE_KEY = 'site.settings.shareable';

    protected $fillable = [
        'title',
        'description',
        'logo',
        'favicon',
        'placeholder_image',
        'maintenance_mode',
        'magicbox',
    ];

    protected function casts(): array
    {
        return [
            'maintenance_mode' => 'boolean',
            'magicbox' => 'array',
        ];
    }

    protected static function booted(): void
    {
        static::saved(fn () => static::forgetShareableCache());
        static::deleted(fn () => static::forgetShareableCache());
    }

    /**
     * @return array<string, mixed>
     */
    public function toShareableArray(): array
    {
        return [
            'title' => $this->title,
            'description' => $this->description,
            'logo' => $this->logo,
            'favicon' => $this->favicon,
            'placeholder_image' => $this->placeholder_image,
            'logo_url' => $this->mediaUrl($this->logo),
            'favicon_url' => $this->mediaUrl($this->favicon),
            'placeholder_image_url' => $this->mediaUrl($this->placeholder_image),
            'maintenance_mode' => (bool) $this->maintenance_mode,
            'magicbox' => $this->magicbox ?? [],
        ];
    }

    /**
     * Veritabanından tek sefer okunur; ayar güncellenince cache temizlenir.
     *
     * @return array<string, mixed>
     */
    public static function cachedForViews(): array
    {
        return Cache::rememberForever(self::VIEW_CACHE_KEY, function () {
            return static::singleton()->fresh()->toShareableArray();
        });
    }

    public static function forgetShareableCache(): void
    {
        Cache::forget(self::VIEW_CACHE_KEY);
    }

    public static function singleton(): self
    {
        $row = static::query()->first();

        if ($row) {
            return $row;
        }

        $row = new static;
        $row->title = config('app.name');
        $row->save();

        return $row;
    }

    public function mediaUrl(?string $path): ?string
    {
        if (! $path) {
            return null;
        }

        if (str_contains($path, '/')) {
            return asset('storage/'.$path);
        }

        return asset($path);
    }
}
