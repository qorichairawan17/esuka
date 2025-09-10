<?php

namespace App\Http\Requests\Pengaturan;

use Illuminate\Foundation\Http\FormRequest;

class AplikasiRequest extends FormRequest
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
            'pengadilanTinggi' => 'required|string|max:255',
            'pengadilanNegeri' => 'required|string|max:255',
            'kodeDipa' => 'required|string|max:255',
            'kodeSuratKuasa' => 'required|string|max:255',
            'provinsi' => 'required|string|max:255',
            'kabupaten' => 'required|string|max:255',
            'kodePos' => 'required|string|max:255',
            'alamat' => 'required|string',
            'website' => 'required|string|max:255|url',
            'facebook' => 'required|string|max:255|url',
            'instagram' => 'required|string|max:255|url',
            'youtube' => 'required|string|max:255|url',
            'kontak' => 'required|string|max:15',
            'email' => 'required|string|max:255|email',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048|file',
            'maintenance' => 'required|boolean'
        ];
    }

    public function messages()
    {
        return [
            'pengadilanTinggi.required' => 'Pengadilan Tinggi harus diisi.',
            'pengadilanTinggi.string'   => 'Pengadilan Tinggi harus berupa teks.',
            'pengadilanTinggi.max'      => 'Pengadilan Tinggi tidak boleh lebih dari 255 karakter.',

            'pengadilanNegeri.required' => 'Pengadilan Negeri harus diisi.',
            'pengadilanNegeri.string'   => 'Pengadilan Negeri harus berupa teks.',
            'pengadilanNegeri.max'      => 'Pengadilan Negeri tidak boleh lebih dari 255 karakter.',

            'kodeDipa.required' => 'Kode Dipa harus diisi.',
            'kodeDipa.string'   => 'Kode Dipa harus berupa teks.',
            'kodeDipa.max'      => 'Kode Dipa tidak boleh lebih dari 255 karakter.',

            'kodeSuratKuasa.required' => 'Kode Surat Kuasa harus diisi.',
            'kodeSuratKuasa.string'   => 'Kode Surat Kuasa harus berupa teks.',
            'kodeSuratKuasa.max'      => 'Kode Surat Kuasa tidak boleh lebih dari 255 karakter.',

            'provinsi.required' => 'Provinsi harus diisi.',
            'provinsi.string'   => 'Provinsi harus berupa teks.',
            'provinsi.max'      => 'Provinsi tidak boleh lebih dari 255 karakter.',

            'kabupaten.required' => 'Kabupaten harus diisi.',
            'kabupaten.string'   => 'Kabupaten harus berupa teks.',
            'kabupaten.max'      => 'Kabupaten tidak boleh lebih dari 255 karakter.',

            'kodePos.required' => 'Kode Pos harus diisi.',
            'kodePos.string'   => 'Kode Pos harus berupa teks.',
            'kodePos.max'      => 'Kode Pos tidak boleh lebih dari 255 karakter.',

            'alamat.required' => 'Alamat harus diisi.',
            'alamat.string'   => 'Alamat harus berupa teks.',

            'website.required' => 'Website harus diisi.',
            'website.string'   => 'Website harus berupa teks.',
            'website.max'      => 'Website tidak boleh lebih dari 255 karakter.',
            'website.url'      => 'Format URL website tidak valid.',

            'facebook.required' => 'Facebook harus diisi.',
            'facebook.string'   => 'Facebook harus berupa teks.',
            'facebook.max'      => 'URL Facebook tidak boleh lebih dari 255 karakter.',
            'facebook.url'      => 'Format URL Facebook tidak valid.',

            'instagram.required' => 'Instagram harus diisi.',
            'instagram.string'   => 'Instagram harus berupa teks.',
            'instagram.max'      => 'URL Instagram tidak boleh lebih dari 255 karakter.',
            'instagram.url'      => 'Format URL Instagram tidak valid.',

            'youtube.required' => 'Youtube harus diisi.',
            'youtube.string'   => 'Youtube harus berupa teks.',
            'youtube.max'      => 'URL Youtube tidak boleh lebih dari 255 karakter.',
            'youtube.url'      => 'Format URL Youtube tidak valid.',

            'kontak.required' => 'Kontak harus diisi.',
            'kontak.string'   => 'Kontak harus berupa teks.',
            'kontak.max'      => 'Kontak tidak boleh lebih dari 15 karakter.',

            'email.required' => 'Email harus diisi.',
            'email.string'   => 'Email harus berupa teks.',
            'email.max'      => 'Email tidak boleh lebih dari 255 karakter.',
            'email.email'    => 'Format email tidak valid.',

            'logo.image'    => 'Logo harus berupa gambar.',
            'logo.mimes'    => 'Format logo harus jpeg, png, jpg, atau gif.',
            'logo.max'      => 'Ukuran logo tidak boleh lebih dari 2MB.',
            'logo.file'     => 'Logo harus berupa file.',

            'maintenance.required' => 'Mode Maintenance harus diisi.',
            'maintenance.boolean'  => 'Nilai Mode Maintenance tidak valid.',
        ];
    }
}
