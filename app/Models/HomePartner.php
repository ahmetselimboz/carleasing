<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HomePartner extends Model
{
    /**
     * @var list<string>
     */
    protected $fillable = [
        'sort_order',
        'is_active',
        'name',
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

    /** @param  \Illuminate\Database\Eloquent\Builder<static>  $query */
    public function scopeActiveOrdered($query)
    {
        return $query->where('is_active', true)->orderBy('sort_order')->orderBy('id');
    }
}
