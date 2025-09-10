<?php

namespace App\Http\Requests\SuratKuasa;

use Illuminate\Foundation\Http\FormRequest;

class SuratKuasaNonAdvokatRequest extends FormRequest
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
    public function rules($isUpdate = false): array
    {
        // For update, file fields are not required. 'sometimes' ensures validation runs only if the field is present.
        $fileRule = $isUpdate ? 'sometimes|nullable' : 'required';

        return [
            'perihal' => 'required|string',
            'jenisSurat' => 'required|string',
            'ktp' => [$fileRule, 'file', 'mimes:pdf', 'max:2048'],
            'ktpp' => [$fileRule, 'file', 'mimes:pdf', 'max:2048'],
            'suratTugas' => [$fileRule, 'file', 'mimes:pdf', 'max:2048'],
            'suratKuasa' => [$fileRule, 'file', 'mimes:pdf', 'max:10240'],
        ];
    }

    public function messages()
    {
        return [
            'perihal.required' => 'Perihal harus diisi.',
            'perihal.string' => 'Perihal harus berupa teks.',
            'jenisSurat.required' => 'Jenis Surat Kuasa harus dipilih.',
            'ktp.required' => 'Kartu Tanda Penduduk harus diunggah.',
            'ktp.file' => 'Kartu Tanda Penduduk harus berupa file.',
            'ktp.mimes' => 'Format Kartu Tanda Penduduk harus pdf.',
            'ktp.max' => 'Ukuran Kartu Tanda Penduduk tidak boleh lebih dari 2MB.',
            'ktpp.required' => 'Kartu Tanda Pegawai harus diunggah.',
            'ktpp.file' => 'Kartu Tanda Pegawai harus berupa file.',
            'ktpp.mimes' => 'Format Kartu Tanda Pegawai harus pdf.',
            'ktpp.max' => 'Ukuran Kartu Tanda Pegawai tidak boleh lebih dari 2MB.',
            'suratTugas.required' => 'Surat Tugas harus diunggah.',
            'suratTugas.file' => 'Surat Tugas harus berupa file.',
            'suratTugas.mimes' => 'Format Surat Tugas harus pdf.',
            'suratTugas.max' => 'Ukuran Surat Tugas tidak boleh lebih dari 2MB.',
            'suratKuasa.required' => 'Surat Kuasa harus diunggah.',
            'suratKuasa.file' => 'Surat Kuasa harus berupa file.',
            'suratKuasa.mimes' => 'Format Surat Kuasa harus pdf.',
            'suratKuasa.max' => 'Ukuran Surat Kuasa tidak boleh lebih dari 10MB.',
        ];
    }
}
