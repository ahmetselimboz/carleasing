<?php

namespace App\Http\Requests\Manage;

use App\Models\Car;
use App\Support\MagicboxForm;
use Illuminate\Foundation\Http\FormRequest;

class UpdateCarRequest extends FormRequest
{
    public function authorize(): bool
    {
        $car = $this->route('car');

        return $car instanceof Car && ($this->user()?->can('update', $car) ?? false);
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_active' => $this->boolean('is_active'),
            'status' => $this->boolean('status'),
            'home_featured' => $this->boolean('home_featured'),
            'remove_image' => $this->boolean('remove_image'),
            'magicbox' => MagicboxForm::toStorage($this->input('mb')),
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'title' => ['nullable', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'brand' => ['nullable', 'string', 'max:255'],
            'model' => ['nullable', 'string', 'max:255'],
            'fuel_type' => ['nullable', 'string', 'max:255'],
            'transmission_type' => ['nullable', 'string', 'max:255'],
            'body_type' => ['nullable', 'string', 'max:255'],
            'is_active' => ['sometimes', 'boolean'],
            'status' => ['sometimes', 'boolean'],
            'home_featured' => ['sometimes', 'boolean'],
            'home_sort_order' => ['nullable', 'integer', 'min:0', 'max:65535'],
            'mb' => ['nullable', 'array'],
            'mb.*.key' => ['nullable', 'string', 'max:100'],
            'mb.*.type' => ['nullable', 'in:string,int,bool'],
            'mb.*.value' => ['nullable', 'string', 'max:65535'],
            'magicbox' => ['nullable', 'array'],
            'image' => ['nullable', 'image', 'max:5120', 'mimes:jpeg,jpg,png,webp,gif'],
            'remove_image' => ['sometimes', 'boolean'],
        ];
    }
}
