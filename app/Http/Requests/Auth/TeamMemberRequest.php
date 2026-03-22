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
        // Get user ID for edit mode — null for create
        $userId = $this->route('id') ?? $this->input('id');

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
                    ? "unique:users,employee_code,{$userId}"  // Edit
                    : 'unique:users,employee_code',            // Create
            ],
            'password'      => [
                $userId ? 'nullable' : 'required',  // Optional on edit, required on create
                'string',
                'min:8',
            ],
            'work_type'     => ['nullable', 'string', 'max:50'],
            'is_active'     => ['nullable', 'boolean'],
            'assigned_to'   => ['nullable', 'exists:users,id'],
            'role' => ['required', 'string', 'in:team_member,customer'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'        => 'Full name is required.',
            'name.min'             => 'Name must be at least 3 characters.',
            'email.required'       => 'Email address is required.',
            'email.unique'         => 'This email is already registered.',
            'employee_code.unique' => 'This employee code is already in use.',
            'password.required'    => 'Password is required for new team members.',
            'password.min'         => 'Password must be at least 8 characters.',
            'role.required'        => 'Please select a role.',
            'role.in'              => 'Invalid role selected.',
            'assigned_to.exists'   => 'Selected manager does not exist.',
        ];
    }
}