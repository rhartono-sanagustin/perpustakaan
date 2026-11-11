<?php

namespace App\Http\Requests\Admin;

use App\Enums\ReturnCondition;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ReturnLoanRequest extends FormRequest
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
        return [
            'return_condition' => ['required', Rule::enum(ReturnCondition::class)],
            'return_notes' => ['nullable', 'string', 'max:1000'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'return_condition' => 'kondisi buku saat kembali',
            'return_notes' => 'catatan pengembalian',
        ];
    }
}
