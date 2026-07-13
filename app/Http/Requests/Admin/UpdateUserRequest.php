<?php

namespace App\Http\Requests\Admin;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()?->isAdmin();
    }

    public function rules(): array
    {
        $userId = $this->route('user')->id;

        return [
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'string', 'email', 'max:255',
                           Rule::unique('users', 'email')->ignore($userId)],
            'role'     => ['required', 'string', 'in:Admin,Staff,Kasir,Gudang'],
            'status'   => ['required', 'string', 'in:Aktif,Nonaktif'],
            // password is optional on update
            'password' => ['nullable', 'confirmed', Password::min(8)->letters()->numbers()],
        ];
    }

    public function messages(): array
    {
        return [
            'email.unique'       => 'Email sudah digunakan oleh pengguna lain.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
        ];
    }
}
