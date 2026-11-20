<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    /**
     * Aturan validasi berdasarkan role pengguna.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $user = $this->user();

        $emailRules = [
            'required',
            'string',
            'lowercase',
            'email',
            'max:255',
            Rule::unique('users', 'email')
                ->ignore($user->user_id, 'user_id'),
        ];

        $phoneRules = [
            'nullable',
            'string',
            'max:20',
        ];

        if ($user->role === 'siswa') {
            return [
                'email' => $emailRules,
                'phone' => $phoneRules,
                'address' => ['nullable', 'string', 'max:255'],
                'district' => ['nullable', 'string', 'max:100'],
                'city' => ['nullable', 'string', 'max:100'],
                'province' => ['nullable', 'string', 'max:100'],
                'parent_name' => ['nullable', 'string', 'max:100'],
                'parent_phone' => ['nullable', 'string', 'max:20'],
            ];
        }

        if ($user->role === 'guru') {
            return [
                'email' => $emailRules,
                'phone' => $phoneRules,
                'address' => ['nullable', 'string', 'max:255'],
            ];
        }

        // Admin tetap menggunakan form profil lama.
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => $emailRules,
        ];
    }
}