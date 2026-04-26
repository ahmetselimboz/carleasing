<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Faq extends Model
{
    use SoftDeletes;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'sort_order',
        'question',
        'answer',
        'answer_body',
        'is_active',
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

    public function answerText(): string
    {
        $long = $this->answer_body;
        if (is_string($long) && $long !== '') {
            return $long;
        }

        return (string) ($this->answer ?? '');
    }

    /** @param  \Illuminate\Database\Eloquent\Builder<static>  $query */
    public function scopeActiveOrdered($query)
    {
        return $query->where('is_active', true)->orderBy('sort_order')->orderBy('id');
    }
}
