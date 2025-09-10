<?php

namespace App\Http\Requests\Pengguna;

use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Validation\Rules\Password;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Encryption\DecryptException;

class AdministratorRequest extends FormRequest
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
        $userId = null;
        if ($this->input('id')) {
            try {
                $userId = Crypt::decrypt($this->input('id'));
            } catch (DecryptException $e) {
                // Jika dekripsi gagal, ID tidak valid.
                // Aturan validasi 'unique' akan gagal jika email sudah ada, yang merupakan perilaku yang benar.
                Log::error('Administrator ID decryption failed: ' . $e->getMessage());
            }
        }

        $passwordRules = [Password::min(8)->mixedCase()->symbols()->numbers()];

        return [
            'nama' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                Rule::unique('users', 'email')->ignore($userId),
            ],
            'password' => $userId
                ? ['nullable', 'string', ...$passwordRules]
                : ['required', 'string', ...$passwordRules],
            'kontak' => 'required|numeric',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048|file',
            'role' => 'required|string',
            'aktif' => 'required|in:0,1',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'nama.required' => 'Nama wajib diisi.',
            'nama.string' => 'Nama harus berupa teks.',
            'nama.max' => 'Nama tidak boleh lebih dari 255 karakter.',

            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah terdaftar, silakan gunakan email lain.',

            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal harus 8 karakter.',
            'password.mixed' => 'Password harus mengandung setidaknya satu huruf besar dan satu huruf kecil.',
            'password.symbols' => 'Password harus mengandung setidaknya satu simbol.',
            'password.numbers' => 'Password harus mengandung setidaknya satu angka.',

            'kontak.required' => 'Kontak wajib diisi.',
            'kontak.numeric' => 'Kontak harus berupa angka.',

            'foto.image' => 'File yang diunggah harus berupa gambar.',
            'foto.mimes' => 'Format foto harus jpeg, png, atau jpg.',
            'foto.max' => 'Ukuran foto maksimal adalah 2MB.',
            'foto.file' => 'Foto yang diunggah harus berupa file.',

            'role.required' => 'Role wajib dipilih.',

            'aktif.required' => 'Status keaktifan wajib dipilih.',
            'aktif.in' => 'Pilihan status keaktifan tidak valid.',
        ];
    }
}
