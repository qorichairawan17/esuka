<?php

namespace App\Http\Requests\SuratKuasa;

use App\Enum\TahapanSuratKuasaEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RejectSuratKuasaRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Assuming only authenticated users with specific roles can reject.
        // Adjust the logic based on your application's authorization needs.
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'id' => 'required|string',
            'tahapan' => [
                'required',
                Rule::in([
                    TahapanSuratKuasaEnum::PerbaikanData->value,
                    TahapanSuratKuasaEnum::PerbaikanPembayaran->value,
                ]),
            ],
            'keterangan' => 'required|string|min:10',
        ];
    }

    public function messages(): array
    {
        return [
            'tahapan.required' => 'Pilih jenis perbaikan yang diperlukan untuk pemohon.',
            'keterangan.required' => 'Alasan penolakan wajib diisi.',
            'keterangan.min' => 'Alasan penolakan minimal 10 karakter.',
        ];
    }
}
