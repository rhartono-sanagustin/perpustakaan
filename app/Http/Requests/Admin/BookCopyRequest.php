<?php

namespace App\Http\Requests\Admin;

use App\Enums\BookCondition;
use App\Enums\BookCopyStatus;
use App\Models\BookCopy;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class BookCopyRequest extends FormRequest
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
        /** @var BookCopy|null $copy */
        $copy = $this->route('copy');

        return [
            'inventory_code' => [
                'required',
                'string',
                'max:50',
                Rule::unique('book_copies', 'inventory_code')->ignore($copy),
            ],
            'shelf_location' => ['nullable', 'string', 'max:100'],
            'condition' => ['required', Rule::enum(BookCondition::class)],
            'status' => ['required', Rule::enum(BookCopyStatus::class)],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function after(): array
    {
        return [
            function (Validator $validator) {
                /** @var BookCopy|null $copy */
                $copy = $this->route('copy');
                $status = $this->input('status');
                $condition = $this->input('condition');
                $hasActiveLoan = $copy?->activeLoan()->exists() ?? false;

                if ($status === BookCopyStatus::Dipinjam->value && ! $hasActiveLoan) {
                    $validator->errors()->add(
                        'status',
                        'Status Dipinjam hanya dapat diatur melalui proses checkout pada fase sirkulasi.'
                    );
                }

                if ($hasActiveLoan && $status !== BookCopyStatus::Dipinjam->value) {
                    $validator->errors()->add(
                        'status',
                        'Eksemplar masih memiliki pinjaman aktif. Catat pengembalian terlebih dahulu.'
                    );
                }

                if ($condition === BookCondition::RusakBerat->value && $status === BookCopyStatus::Tersedia->value) {
                    $validator->errors()->add(
                        'status',
                        'Eksemplar dengan kondisi rusak berat tidak boleh berstatus Tersedia.'
                    );
                }
            },
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'inventory_code' => 'kode inventaris',
            'shelf_location' => 'lokasi rak',
            'condition' => 'kondisi',
            'status' => 'status',
            'notes' => 'catatan',
        ];
    }
}
