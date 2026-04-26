<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PageCategory extends Model
{
    /**
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'is_active',
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

    /**
     * @return HasMany<Page, $this>
     */
    public function pages(): HasMany
    {
        return $this->hasMany(Page::class);
    }
}
