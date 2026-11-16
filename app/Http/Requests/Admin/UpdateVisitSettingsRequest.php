<?php

namespace App\Http\Requests\Admin;

use App\Models\LibrarySetting;
use Illuminate\Foundation\Http\FormRequest;

class UpdateVisitSettingsRequest extends FormRequest
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
            'visit_kiosk_enabled' => ['nullable', 'boolean'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'visit_kiosk_enabled' => 'kiosk kunjungan mandiri',
        ];
    }
}
