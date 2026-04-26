<?php

namespace App\Http\Requests\Manage;

use App\Models\Slider;
use Illuminate\Foundation\Http\FormRequest;

class UpdateSliderRequest extends FormRequest
{
    public function authorize(): bool
    {
        $slider = $this->route('slider');

        return $slider instanceof Slider && ($this->user()?->can('update', $slider) ?? false);
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_active' => $this->boolean('is_active'),
            'remove_image_1' => $this->boolean('remove_image_1'),
            'remove_image_2' => $this->boolean('remove_image_2'),
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
            'badge' => ['nullable', 'string', 'max:120'],
            'title' => ['nullable', 'string', 'max:255'],
            'title_highlight' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:255'],
            'subtitle' => ['nullable', 'string', 'max:5000'],
            'link' => ['nullable', 'string', 'max:2048'],
            'image_1' => ['nullable', 'image', 'max:8192', 'mimes:jpeg,jpg,png,webp,avif,gif'],
            'image_2' => ['nullable', 'image', 'max:8192', 'mimes:jpeg,jpg,png,webp,avif,gif'],
            'remove_image_1' => ['sometimes', 'boolean'],
            'remove_image_2' => ['sometimes', 'boolean'],
        ];
    }
}
