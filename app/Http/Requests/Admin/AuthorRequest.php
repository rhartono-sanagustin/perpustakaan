<?php

namespace App\Http\Requests\Admin;

use App\Models\Author;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AuthorRequest extends FormRequest
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
        /** @var Author|null $author */
        $author = $this->route('author');

        return [
            'name' => [
                'required',
                'string',
                'max:150',
                Rule::unique('authors', 'name')->ignore($author),
            ],
            'bio' => ['nullable', 'string', 'max:2000'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'name' => 'nama penulis',
            'bio' => 'biografi',
        ];
    }
}
