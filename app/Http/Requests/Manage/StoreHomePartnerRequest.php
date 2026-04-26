<?php

namespace App\Http\Requests\Manage;

use App\Models\HomePartner;
use Illuminate\Foundation\Http\FormRequest;

class StoreHomePartnerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create', HomePartner::class) ?? false;
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
        ];
    }
}
