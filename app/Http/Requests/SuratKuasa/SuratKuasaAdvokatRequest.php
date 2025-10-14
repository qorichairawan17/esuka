<?php

namespace App\Http\Requests\SuratKuasa;

use Illuminate\Foundation\Http\FormRequest;

class SuratKuasaAdvokatRequest extends FormRequest
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
            'ktp' => [$fileRule, 'file', 'mimes:pdf', 'max:5120'],
            'kta' => [$fileRule, 'file', 'mimes:pdf', 'max:5120'],
            'bas' => [$fileRule, 'file', 'mimes:pdf', 'max:5120'],
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
            'ktp.max' => 'Ukuran Kartu Tanda Penduduk tidak boleh lebih dari 5MB.',
            'kta.required' => 'Kartu Tanda Anggota harus diunggah.',
            'kta.file' => 'Kartu Tanda Anggota harus berupa file.',
            'kta.mimes' => 'Format Kartu Tanda Anggota harus pdf.',
            'kta.max' => 'Ukuran Kartu Tanda Anggota tidak boleh lebih dari 5MB.',
            'bas.required' => 'Berita Acara Sumpah harus diunggah.',
            'bas.file' => 'Berita Acara Sumpah harus berupa file.',
            'bas.mimes' => 'Format Berita Acara Sumpah harus pdf.',
            'bas.max' => 'Ukuran Berita Acara Sumpah tidak boleh lebih dari 5MB.',
            'suratKuasa.required' => 'Surat Kuasa harus diunggah.',
            'suratKuasa.file' => 'Surat Kuasa harus berupa file.',
            'suratKuasa.mimes' => 'Format Surat Kuasa harus pdf.',
            'suratKuasa.max' => 'Ukuran Surat Kuasa tidak boleh lebih dari 10MB.',
        ];
    }
}
