<?php

namespace App\Http\Requests\SuratKuasa;

use Illuminate\Foundation\Http\FormRequest;

class ApproveSuratKuasaRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Assuming only authenticated users with specific roles can approve.
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
            'nomor_surat_kuasa' => 'required|string',
            'panitera_id' => 'required|exists:sk_panitera,id',
        ];
    }

    public function messages(): array
    {
        return [
            'nomor_surat_kuasa.required' => 'Nomor surat kuasa wajib diisi.',
            'panitera_id.required' => 'Panitera wajib dipilih.',
            'panitera_id.exists' => 'Panitera yang dipilih tidak valid.',
        ];
    }
}
