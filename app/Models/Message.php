<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Message extends Model
{
    use HasFactory, SoftDeletes;

    public const CATEGORY_INFO = 'info';
    public const CATEGORY_SUGGESTION = 'suggestion';
    public const CATEGORY_COMPLAINT = 'complaint';
    public const CATEGORY_THANKS = 'thanks';

    /**
     * @var list<string>
     */
    protected $fillable = [
        'category',
        'name',
        'surname',
        'email',
        'phone_number',
        'content',
        'read_at',
        'ip_address',
        'user_agent',
    ];

    /**
     * @return array<string, string>
     */
    public static function categoryLabels(): array
    {
        return [
            self::CATEGORY_INFO => 'Bilgi',
            self::CATEGORY_SUGGESTION => 'Öneri',
            self::CATEGORY_COMPLAINT => 'Şikayet',
            self::CATEGORY_THANKS => 'Teşekkür',
        ];
    }

    public function categoryLabel(): string
    {
        return self::categoryLabels()[$this->category] ?? 'Diğer';
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'read_at' => 'datetime',
        ];
    }
}
