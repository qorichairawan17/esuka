<?php

namespace App\Http\Requests\Pengguna;

use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Foundation\Http\FormRequest;

class PaniteraRequest extends FormRequest
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
        $paniteraId = null;
        if ($this->input('id')) {
            try {
                // Decrypt the ID from the form input to use in the 'ignore' rule.
                // This is necessary because the form sends an encrypted ID.
                $paniteraId = Crypt::decrypt($this->input('id'));
            } catch (\Illuminate\Contracts\Encryption\DecryptException $e) {
                // If decryption fails, it's invalid input. Let validation proceed without an ignored ID.
                Log::error('Panitera ID decryption failed: ' . $e->getMessage());
            }
        }

        return [
            'nip' => [
                'required',
                'numeric',
                Rule::unique('sk_panitera', 'nip')->ignore($paniteraId),
            ],
            'nama' => ['required', 'string', 'max:255'],
            'jabatan' => ['required', 'string', 'max:255'],
            'status' => ['required', 'string', 'max:255'],
            'aktif' => ['required', 'in:1,0'],
        ];
    }

    public function messages(): array
    {
        return [
            'nip.required' => 'NIP wajib diisi.',
            'nip.unique' => 'NIP sudah terdaftar.',
            'nip.numeric' => 'NIP harus berupa angka.',

            'nama.required' => 'Nama wajib diisi.',
            'nama.string' => 'Nama harus berupa teks.',
            'nama.max' => 'Nama tidak boleh lebih dari :max karakter.',

            'jabatan.required' => 'Jabatan wajib diisi.',
            'jabatan.string' => 'Jabatan harus berupa teks.',
            'jabatan.max' => 'Jabatan tidak boleh lebih dari :max karakter.',

            'status.required' => 'Status wajib diisi.',
            'status.string' => 'Status harus berupa teks.',
            'status.max' => 'Status tidak boleh lebih dari :max karakter.',

            'aktif.required' => 'Status keaktifan wajib diisi.',
            'aktif.in' => 'Status keaktifan tidak valid. Pilih antara "Ya" atau "Tidak".',
        ];
    }
}
