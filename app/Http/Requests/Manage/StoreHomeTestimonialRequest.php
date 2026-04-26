<?php

namespace App\Http\Requests\Manage;

use App\Models\HomeTestimonial;
use Illuminate\Foundation\Http\FormRequest;

class StoreHomeTestimonialRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create', HomeTestimonial::class) ?? false;
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
            'name' => ['required', 'string', 'max:120'],
            'role' => ['nullable', 'string', 'max:120'],
            'quote' => ['required', 'string', 'max:5000'],
            'rating' => ['nullable', 'integer', 'min:1', 'max:5'],
            'avatar' => ['nullable', 'image', 'max:4096', 'mimes:jpeg,jpg,png,webp,gif'],
        ];
    }
}
