<?php

namespace App\Http\Requests\Admin;

use App\Models\CarouselSlide;
use Closure;
use Illuminate\Foundation\Http\FormRequest;

class CarouselSlideRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() === true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        /** @var CarouselSlide|null $slide */
        $slide = $this->route('slide');

        return [
            'title' => ['nullable', 'string', 'max:150'],
            'caption' => ['nullable', 'string', 'max:500'],
            'alt_text' => ['nullable', 'string', 'max:255'],
            'link_url' => ['nullable', 'string', 'max:2048', $this->linkRule()],
            'image' => [
                $slide ? 'nullable' : 'required',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:'.config('library.carousel.image_max_kb', 2048),
            ],
            'is_active' => ['nullable', 'boolean'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'title' => 'judul',
            'caption' => 'keterangan',
            'alt_text' => 'teks alternatif',
            'link_url' => 'tautan',
            'image' => 'gambar',
            'is_active' => 'status aktif',
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'image.required' => 'Gambar slide wajib diunggah.',
            'image.image' => 'Gambar slide harus berupa gambar.',
            'image.mimes' => 'Gambar slide harus berformat JPG, PNG, atau WEBP.',
            'image.max' => 'Ukuran gambar slide maksimal 2 MB.',
        ];
    }

    private function linkRule(): Closure
    {
        return function (string $attribute, mixed $value, Closure $fail): void {
            if (! is_string($value) || trim($value) === '') {
                return;
            }

            $value = trim($value);

            if (str_starts_with($value, '/')) {
                return;
            }

            $scheme = parse_url($value, PHP_URL_SCHEME);

            if (! in_array($scheme, ['http', 'https'], true) || filter_var($value, FILTER_VALIDATE_URL) === false) {
                $fail('Tautan harus berupa path internal yang diawali "/" atau URL http/https yang valid.');
            }
        };
    }
}
