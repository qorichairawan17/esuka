<?php

namespace App\Http\Requests\Profile;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class UpdatePasswordRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'passwordLama' => ['required', 'current_password'],
            'passwordBaru' => [
                'required',
                'confirmed',
                'different:passwordLama',
                Password::min(8)
                    ->mixedCase()
                    ->numbers()
                    ->symbols()
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'passwordLama.required' => 'Password lama wajib diisi.',
            'passwordLama.current_password' => 'Password lama yang Anda masukkan salah.',
            'passwordBaru.required' => 'Password baru wajib diisi.',
            'passwordBaru.confirmed' => 'Konfirmasi password baru tidak cocok.',
            'passwordBaru.different' => 'Password baru tidak boleh sama dengan password lama.',
            'passwordBaru.min' => 'Password baru minimal harus 8 karakter.',
            'passwordBaru.mixedCase' => 'Password baru harus mengandung huruf kapital dan huruf kecil.',
            'passwordBaru.numbers' => 'Password baru harus mengandung setidaknya satu angka.',
            'passwordBaru.symbols' => 'Password baru harus mengandung setidaknya satu simbol.',
        ];
    }
}
