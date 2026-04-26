<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateMenusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'navbar' => ['nullable', 'array'],
            'navbar.*.type' => ['nullable', 'string', 'in:custom,page,group'],
            'navbar.*.parent' => ['nullable', 'string', 'max:120'],
            'navbar.*.page_id' => ['nullable', 'integer', 'exists:pages,id'],
            'navbar.*.label' => ['nullable', 'string', 'max:120'],
            'navbar.*.url' => ['nullable', 'string', 'max:2000'],
            'footer' => ['nullable', 'array'],
            'footer.*.type' => ['nullable', 'string', 'in:custom,page,group'],
            'footer.*.parent' => ['nullable', 'string', 'max:120'],
            'footer.*.page_id' => ['nullable', 'integer', 'exists:pages,id'],
            'footer.*.label' => ['nullable', 'string', 'max:120'],
            'footer.*.url' => ['nullable', 'string', 'max:2000'],
        ];
    }
}
