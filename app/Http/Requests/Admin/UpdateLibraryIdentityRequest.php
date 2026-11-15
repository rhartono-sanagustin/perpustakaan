<?php

namespace App\Http\Requests\Admin;

use App\Models\LibrarySetting;
use Illuminate\Foundation\Http\FormRequest;

class UpdateLibraryIdentityRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('viewAny', LibrarySetting::class) === true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'school_name' => ['required', 'string', 'max:'.config('library.identity_definitions.school_name.max', 120)],
            'library_name' => ['required', 'string', 'max:'.config('library.identity_definitions.library_name.max', 100)],
            'tagline' => ['nullable', 'string', 'max:'.config('library.identity_definitions.tagline.max', 160)],
            'logo' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:'.config('library.branding.logo_max_kb', 1024),
            ],
            'remove_logo' => ['nullable', 'boolean'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'school_name' => 'nama sekolah',
            'library_name' => 'nama perpustakaan',
            'tagline' => 'tagline',
            'logo' => 'logo',
            'remove_logo' => 'hapus logo',
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'logo.image' => 'Logo harus berupa gambar.',
            'logo.mimes' => 'Logo harus berformat JPG, PNG, atau WEBP.',
            'logo.max' => 'Ukuran logo maksimal 1 MB.',
        ];
    }
}
