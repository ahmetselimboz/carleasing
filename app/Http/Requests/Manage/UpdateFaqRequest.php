<?php

namespace App\Http\Requests\Manage;

use App\Models\Faq;
use Illuminate\Foundation\Http\FormRequest;

class UpdateFaqRequest extends FormRequest
{
    public function authorize(): bool
    {
        $faq = $this->route('faq');

        return $faq instanceof Faq && ($this->user()?->can('update', $faq) ?? false);
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
            'question' => ['required', 'string', 'max:500'],
            'answer_body' => ['nullable', 'string', 'max:20000'],
        ];
    }
}
