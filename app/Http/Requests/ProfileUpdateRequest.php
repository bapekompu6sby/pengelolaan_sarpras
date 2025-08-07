<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255', Rule::unique(User::class)->ignore($this->user()->id)],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique(User::class)->ignore($this->user()->id)],
        ];
    }

    // custom message
    public function messages()
    {
        return [
            'name.required' => 'Username harus diisi',
            'name.unique' => 'Username sudah digunakan',
            'email.required' => 'Email harus diisi',
            'email.unique' => 'Email sudah digunakan',
            'email.email' => 'Email tidak valid',
        ];
    }
}
