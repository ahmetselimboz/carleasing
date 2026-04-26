<?php

namespace App\Http\Requests\Manage;

use App\Models\PageCategory;
use Illuminate\Foundation\Http\FormRequest;

class UpdatePageCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        $pageCategory = $this->route('page_category');

        return $pageCategory instanceof PageCategory && ($this->user()?->can('update', $pageCategory) ?? false);
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
