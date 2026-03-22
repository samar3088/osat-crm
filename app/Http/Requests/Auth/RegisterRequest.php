<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'          => ['required', 'string', 'min:3', 'max:100'],
            'employee_code' => [
                'nullable',
                'string',
                'max:20',
                'unique:users,employee_code',
            ],
            'email'         => [
                'required',
                'string',
                'email',
                'max:255',
                'unique:users,email',
            ],
            'password'      => ['required', 'string', 'min:8', 'confirmed'],
            'terms'         => ['accepted'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'          => 'Full name is required.',
            'name.min'               => 'Name must be at least 3 characters.',
            'email.required'         => 'Email address is required.',
            'email.unique'           => 'This email is already registered.',
            'employee_code.unique'   => 'This employee code is already in use.',
            'password.min'           => 'Password must be at least 8 characters.',
            'password.confirmed'     => 'Passwords do not match.',
            'terms.accepted'         => 'You must accept the Terms of Service.',
        ];
    }
}