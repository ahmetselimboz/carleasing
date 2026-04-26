<?php

namespace App\Http\Requests\Manage;

use App\Models\Page;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePageRequest extends FormRequest
{
    public function authorize(): bool
    {
        $page = $this->route('page');

        return $page instanceof Page && ($this->user()?->can('update', $page) ?? false);
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
        $page = $this->route('page');
        $pageId = $page instanceof Page ? $page->getKey() : null;

        return [
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', Rule::unique('pages', 'slug')->ignore($pageId)],
            'description' => ['nullable', 'string'],
            'page_category_id' => ['required', 'integer', 'exists:page_categories,id'],
            'is_active' => ['sometimes', 'boolean'],
            'magicbox' => ['nullable', 'json'],
        ];
    }
}
