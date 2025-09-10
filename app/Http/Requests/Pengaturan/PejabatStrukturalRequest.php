<?php

namespace App\Http\Requests\Pengaturan;

use Illuminate\Foundation\Http\FormRequest;

class PejabatStrukturalRequest extends FormRequest
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
        // Find the existing record. We assume a single-row table for settings.
        $pejabat = \App\Models\Pengaturan\PejabatStrukturalModel::first();

        // Base rules for photos
        $photoRules = 'image|mimes:jpeg,png,jpg,gif|max:5120|file';

        return [
            'ketua' => 'required|string|max:255',
            'foto_ketua' => ($pejabat && $pejabat->foto_ketua) ? 'nullable|' . $photoRules : 'required|' . $photoRules,
            'wakil_ketua' => 'required|string|max:255',
            'foto_wakil_ketua' => ($pejabat && $pejabat->foto_wakil_ketua) ? 'nullable|' . $photoRules : 'required|' . $photoRules,
            'panitera' => 'required|string|max:255',
            'foto_panitera' => ($pejabat && $pejabat->foto_panitera) ? 'nullable|' . $photoRules : 'required|' . $photoRules,
            'sekretaris' => 'required|string|max:255',
            'foto_sekretaris' => ($pejabat && $pejabat->foto_sekretaris) ? 'nullable|' . $photoRules : 'required|' . $photoRules
        ];
    }

    public function messages(): array
    {
        return [
            'ketua.required' => 'Nama Ketua harus diisi.',
            'ketua.string' => 'Nama Ketua harus berupa teks.',
            'ketua.max' => 'Nama Ketua tidak boleh lebih dari 255 karakter.',

            'foto_ketua.required' => 'Foto Ketua harus diunggah.',
            'foto_ketua.image' => 'Foto Ketua harus berupa gambar.',
            'foto_ketua.mimes' => 'Format Foto Ketua harus jpeg, png, jpg, atau gif.',
            'foto_ketua.max' => 'Ukuran Foto Ketua tidak boleh lebih dari 5MB.',
            'foto_ketua.file' => 'Foto Ketua harus berupa file.',

            'wakil_ketua.required' => 'Nama Wakil Ketua harus diisi.',
            'wakil_ketua.string' => 'Nama Wakil Ketua harus berupa teks.',
            'wakil_ketua.max' => 'Nama Wakil Ketua tidak boleh lebih dari 255 karakter.',

            'foto_wakil_ketua.required' => 'Foto Wakil Ketua harus diunggah.',
            'foto_wakil_ketua.image' => 'Foto Wakil Ketua harus berupa gambar.',
            'foto_wakil_ketua.mimes' => 'Format Foto Wakil Ketua harus jpeg, png, jpg, atau gif.',
            'foto_wakil_ketua.max' => 'Ukuran Foto Wakil Ketua tidak boleh lebih dari 5MB.',
            'foto_wakil_ketua.file' => 'Foto Wakil Ketua harus berupa file.',

            'panitera.required' => 'Nama Panitera harus diisi.',
            'panitera.string' => 'Nama Panitera harus berupa teks.',
            'panitera.max' => 'Nama Panitera tidak boleh lebih dari 255 karakter.',

            'foto_panitera.required' => 'Foto Panitera harus diunggah.',
            'foto_panitera.image' => 'Foto Panitera harus berupa gambar.',
            'foto_panitera.mimes' => 'Format Foto Panitera harus jpeg, png, jpg, atau gif.',
            'foto_panitera.max' => 'Ukuran Foto Panitera tidak boleh lebih dari 5MB.',
            'foto_panitera.file' => 'Foto Panitera harus berupa file.',

            'sekretaris.required' => 'Nama Sekretaris harus diisi.',
            'sekretaris.string' => 'Nama Sekretaris harus berupa teks.',
            'sekretaris.max' => 'Nama Sekretaris tidak boleh lebih dari 255 karakter.',

            'foto_sekretaris.required' => 'Foto Sekretaris harus diunggah.',
            'foto_sekretaris.image' => 'Foto Sekretaris harus berupa gambar.',
            'foto_sekretaris.mimes' => 'Format Foto Sekretaris harus jpeg, png, jpg, atau gif.',
            'foto_sekretaris.max' => 'Ukuran Foto Sekretaris tidak boleh lebih dari 5MB.',
            'foto_sekretaris.file' => 'Foto Sekretaris harus berupa file.',
        ];
    }
}
