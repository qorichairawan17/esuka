<?php

namespace App\Http\Requests\Profile;

use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Http\FormRequest;

class UpdateProfileRequest extends FormRequest
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
        $userId = Auth::id();
        return [
            'namaDepan' => 'required|string|max:100',
            'namaBelakang' => 'required|string|max:100',
            'email' => 'required|email|unique:users,email,' . $userId,
            'kontak' => 'required|numeric',
            'tanggalLahir' => 'required|date_format:d-m-Y',
            'jenisKelamin' => 'required|in:Laki-Laki,Perempuan',
            'alamat' => 'required|string|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'namaDepan.required' => 'Nama depan wajib diisi.',
            'namaBelakang.required' => 'Nama depan wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah digunakan oleh pengguna lain.',
            'kontak.required' => 'Kontak wajib diisi.',
            'kontak.numeric' => 'Kontak harus berupa angka.',
            'tanggalLahir.required' => 'Tanggal lahir wajib diisi.',
            'tanggalLahir.date_format' => 'Format tanggal lahir harus dd-mm-yyyy.',
            'jenisKelamin.required' => 'Jenis kelamin wajib dipilih.',
            'jenisKelamin.in' => 'Pilihan jenis kelamin tidak valid.',
            'alamat.required' => 'Alamat wajib diisi.',
        ];
    }
}
