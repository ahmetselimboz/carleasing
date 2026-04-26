<?php

namespace App\Http\Requests\Manage;

use App\Models\HomeServiceTile;
use Illuminate\Foundation\Http\FormRequest;

class StoreHomeServiceTileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create', HomeServiceTile::class) ?? false;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_active' => $this->boolean('is_active'),
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:65535'],
            'is_active' => ['sometimes', 'boolean'],
            'icon' => ['nullable', 'string', 'max:120'],
            'title' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:5000'],
            'link_url' => ['nullable', 'string', 'max:2048'],
            'image' => ['nullable', 'image', 'max:8192', 'mimes:jpeg,jpg,png,webp,avif,gif'],
        ];
    }
}
