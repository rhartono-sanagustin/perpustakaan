<?php

namespace App\Http\Requests\Admin;

use App\Models\Book;
use Closure;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\UploadedFile;

class ImportBooksRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('import', Book::class) === true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $maxFileKb = (int) config('library.imports.max_file_kb', 5120);

        return [
            'file' => [
                'required',
                'file',
                'max:'.$maxFileKb,
                function (string $attribute, mixed $value, Closure $fail): void {
                    if (! $value instanceof UploadedFile) {
                        return;
                    }

                    $extension = strtolower($value->getClientOriginalExtension());

                    if (! in_array($extension, ['csv', 'xlsx'], true)) {
                        $fail('Berkas harus berformat CSV atau XLSX.');
                    }
                },
            ],
            'create_missing' => ['nullable', 'boolean'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        $maxFileMb = round((int) config('library.imports.max_file_kb', 5120) / 1024, 1);

        return [
            'file.required' => 'Pilih berkas yang akan diimpor.',
            'file.file' => 'Berkas impor tidak valid.',
            'file.max' => 'Ukuran berkas maksimal '.$maxFileMb.' MB.',
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'file' => 'berkas',
            'create_missing' => 'buat data master otomatis',
        ];
    }
}
