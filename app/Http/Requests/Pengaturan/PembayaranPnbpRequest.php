<?php

namespace App\Http\Requests\Pengaturan;

use Illuminate\Foundation\Http\FormRequest;

class PembayaranPnbpRequest extends FormRequest
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
            'namaBank' => 'required|string|max:255',
            'nomorRekening' => 'required|string|max:255',
            'logoBank' => 'required|image|mimes:jpeg,png,jpg,gif|max:5120|file',
            'qris' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120|file',
        ];
    }

    public function messages()
    {
        return [
            'namaBank.required' => 'Bank Rekening harus diisi.',
            'namaBank.string'   => 'Bank Rekening harus berupa teks.',
            'namaBank.max'      => 'Bank Rekening tidak boleh lebih dari 255 karakter.',

            'nomorRekening.required' => 'Nomor Rekening harus diisi.',
            'nomorRekening.string'   => 'Nomor Rekening harus berupa teks.',
            'nomorRekening.max'      => 'Nomor Rekening tidak boleh lebih dari 255 karakter.',

            'logoBank.required' => 'Logo Bank harus diunggah.',
            'logoBank.image'    => 'Logo Bank harus berupa gambar.',
            'logoBank.mimes'    => 'Format Logo Bank harus jpeg, png, jpg, atau gif.',
            'logoBank.max'      => 'Ukuran Logo Bank tidak boleh lebih dari 5MB.',
            'logoBank.file'     => 'Logo Bank harus berupa file.',

            'qris.image'    => 'QRIS harus berupa gambar.',
            'qris.mimes'    => 'Format QRIS harus jpeg, png, jpg, atau gif.',
            'qris.max'      => 'Ukuran QRIS tidak boleh lebih dari 5MB.',
            'qris.file'     => 'QRIS harus berupa file.',
        ];
    }
}
