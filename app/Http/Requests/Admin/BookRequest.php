<?php

namespace App\Http\Requests\Admin;

use App\Models\Book;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BookRequest extends FormRequest
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
        /** @var Book|null $book */
        $book = $this->route('book');

        return [
            'title' => ['required', 'string', 'max:255'],
            'category_id' => ['required', 'integer', 'exists:categories,id'],
            'publisher_id' => ['nullable', 'integer', 'exists:publishers,id'],
            'authors' => ['required', 'array', 'min:1'],
            'authors.*' => ['integer', 'distinct', 'exists:authors,id'],
            'isbn' => [
                'nullable',
                'string',
                'max:32',
                Rule::unique('books', 'isbn')->ignore($book),
            ],
            'publication_year' => ['nullable', 'integer', 'min:1000', 'max:'.(date('Y') + 1)],
            'edition' => ['nullable', 'string', 'max:100'],
            'language' => ['required', 'string', 'max:50'],
            'summary' => ['nullable', 'string', 'max:5000'],
            'cover' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'remove_cover' => ['nullable', 'boolean'],
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
            'category_id' => 'kategori',
            'publisher_id' => 'penerbit',
            'authors' => 'penulis',
            'authors.*' => 'penulis',
            'isbn' => 'ISBN',
            'publication_year' => 'tahun terbit',
            'edition' => 'edisi',
            'language' => 'bahasa',
            'summary' => 'ringkasan',
            'cover' => 'sampul',
            'remove_cover' => 'hapus sampul',
            'is_active' => 'status katalog',
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'authors.required' => 'Pilih minimal satu penulis.',
            'cover.image' => 'Sampul harus berupa gambar.',
            'cover.mimes' => 'Sampul harus berformat JPG, PNG, atau WEBP.',
            'cover.max' => 'Ukuran sampul maksimal 2 MB.',
        ];
    }
}
