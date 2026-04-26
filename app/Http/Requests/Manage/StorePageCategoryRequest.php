<?php

namespace App\Http\Requests\Manage;

use App\Models\PageCategory;
use Illuminate\Foundation\Http\FormRequest;

class StorePageCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create', PageCategory::class) ?? false;
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
            'name' => ['required', 'string', 'max:180'],
            'is_active' => ['sometimes', 'boolean'],
            'magicbox' => ['nullable', 'json'],
        ];
    }
}
