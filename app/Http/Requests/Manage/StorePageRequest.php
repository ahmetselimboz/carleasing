<?php

namespace App\Http\Requests\Manage;

use App\Models\Page;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create', Page::class) ?? false;
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
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', Rule::unique('pages', 'slug')],
            'description' => ['nullable', 'string'],
            'page_category_id' => ['required', 'integer', 'exists:page_categories,id'],
            'is_active' => ['sometimes', 'boolean'],
            'magicbox' => ['nullable', 'array'],
        ];
    }
}
