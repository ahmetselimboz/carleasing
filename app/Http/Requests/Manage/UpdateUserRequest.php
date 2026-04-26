<?php

namespace App\Http\Requests\Manage;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        /** @var User $target */
        $target = $this->route('user');

        return $this->user()?->can('update', $target) ?? false;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        /** @var User $target */
        $target = $this->route('user');

        $roleRule = ['required', 'string', Rule::in([User::ROLE_ADMIN, User::ROLE_CUSTOMER_SERVICE])];

        if ($this->user()?->role === User::ROLE_CUSTOMER_SERVICE) {
            $roleRule = ['required', 'string', Rule::in([User::ROLE_CUSTOMER_SERVICE])];
        }

        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($target->id)],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'phone' => ['nullable', 'string', 'max:50'],
            'role' => $roleRule,
            'active' => ['sometimes', 'boolean'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'role.in' => 'Bu rol için düzenleme yetkiniz yok.',
        ];
    }
}
