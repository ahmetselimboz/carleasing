<?php

namespace App\Http\Requests\Manage;

use App\Models\Reference;
use Illuminate\Foundation\Http\FormRequest;

class StoreReferenceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create', Reference::class) ?? false;
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
            'name' => ['nullable', 'string', 'max:180'],
            'title' => ['nullable', 'string', 'max:180'],
            'image' => ['nullable', 'image', 'max:4096', 'mimes:jpeg,jpg,png,webp,gif'],
            'link' => ['nullable', 'url', 'max:2048'],
            'detail' => ['nullable', 'string', 'max:5000'],
            'is_active' => ['sometimes', 'boolean'],
            'magicbox' => ['nullable', 'json'],
        ];
    }
}
