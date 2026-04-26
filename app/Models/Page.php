<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Page extends Model
{
    use SoftDeletes;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'title',
        'slug',
        'slug_hash',
        'description',
        'page_category_id',
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
     * @return BelongsTo<PageCategory, $this>
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(PageCategory::class, 'page_category_id');
    }
}
