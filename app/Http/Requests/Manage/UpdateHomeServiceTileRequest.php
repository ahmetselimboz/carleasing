<?php

namespace App\Http\Requests\Manage;

use App\Models\HomeServiceTile;
use Illuminate\Foundation\Http\FormRequest;

class UpdateHomeServiceTileRequest extends FormRequest
{
    public function authorize(): bool
    {
        $tile = $this->route('home_service_tile');

        return $tile instanceof HomeServiceTile && ($this->user()?->can('update', $tile) ?? false);
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_active' => $this->boolean('is_active'),
            'remove_image' => $this->boolean('remove_image'),
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
            'remove_image' => ['sometimes', 'boolean'],
        ];
    }
}
