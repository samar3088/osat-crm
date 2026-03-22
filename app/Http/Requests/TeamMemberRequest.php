<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TeamMemberRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        // Get ID from route for edit mode
        $userId = $this->route('teamMember')?->id;

        return [
            'name'          => ['required', 'string', 'min:3', 'max:100'],
            'email'         => [
                'required',
                'email',
                'max:255',
                $userId
                    ? "unique:users,email,{$userId}"  // Edit — ignore own record
                    : 'unique:users,email',            // Create — must be unique
            ],
            'employee_code' => [
                'nullable',
                'string',
                'max:50',
                $userId
                    ? "unique:users,employee_code,{$userId}"
                    : 'unique:users,employee_code',
            ],
            'password'      => [
                $userId ? 'nullable' : 'required',   // Optional on edit
                'nullable',
                'string',
                'min:8',
            ],
            'work_type'     => ['nullable', 'string', 'max:50'],
            'is_active'     => ['nullable'],
            'assigned_to'   => ['nullable', 'exists:users,id'],
            'role'          => ['nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'        => 'Full name is required.',
            'name.min'             => 'Name must be at least 3 characters.',
            'email.required'       => 'Email address is required.',
            'email.unique'         => 'This email is already registered by another user.',
            'employee_code.unique' => 'This employee code is already in use.',
            'password.required'    => 'Password is required for new team members.',
            'password.min'         => 'Password must be at least 8 characters.',
            'assigned_to.exists'   => 'Selected manager does not exist.',
        ];
    }
}