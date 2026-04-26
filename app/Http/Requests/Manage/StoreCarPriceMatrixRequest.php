<?php

namespace App\Http\Requests\Manage;

use App\Models\Car;
use App\Models\CarPriceMatrix;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCarPriceMatrixRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create', CarPriceMatrix::class) ?? false;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_active' => $this->boolean('is_active', true),
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $car = $this->route('car');
        assert($car instanceof Car);

        return [
            'car_package_id' => [
                'required',
                'exists:car_packages,id',
                Rule::unique('car_price_matrix', 'car_package_id')->where(function ($query) use ($car) {
                    return $query
                        ->where('car_id', $car->id)
                        ->where('car_duration_id', (int) $this->input('car_duration_id'))
                        ->where('car_kilometer_option_id', (int) $this->input('car_kilometer_option_id'))
                        ->where('car_down_payment_id', (int) $this->input('car_down_payment_id'));
                }),
            ],
            'car_duration_id' => ['required', 'exists:car_durations,id'],
            'car_kilometer_option_id' => ['required', 'exists:car_kilometer_options,id'],
            'car_down_payment_id' => ['required', 'exists:car_down_payments,id'],
            'monthly_price' => ['required', 'string', 'max:255'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'car_package_id.unique' => 'Bu araç için aynı paket, süre, kilometre ve peşinat kombinasyonu zaten tanımlı.',
        ];
    }
}
