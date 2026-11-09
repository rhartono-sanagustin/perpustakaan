<?php

namespace App\Http\Requests\Admin;

use App\Models\Publisher;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PublisherRequest extends FormRequest
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
        /** @var Publisher|null $publisher */
        $publisher = $this->route('publisher');

        return [
            'name' => [
                'required',
                'string',
                'max:150',
                Rule::unique('publishers', 'name')->ignore($publisher),
            ],
            'address' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:255'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'name' => 'nama penerbit',
            'address' => 'alamat',
            'phone' => 'telepon',
            'email' => 'email',
        ];
    }
}
