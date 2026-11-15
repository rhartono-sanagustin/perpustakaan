<?php

namespace App\Http\Requests\Admin;

use App\Models\LibrarySetting;
use Illuminate\Foundation\Http\FormRequest;

class UpdateLibrarySettingsRequest extends FormRequest
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
        $rules = [];

        foreach (config('library.setting_definitions', []) as $key => $definition) {
            $rules[$key] = [
                'required',
                'integer',
                'between:'.$definition['min'].','.$definition['max'],
            ];
        }

        return $rules;
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        $attributes = [];

        foreach (config('library.setting_definitions', []) as $key => $definition) {
            $attributes[$key] = mb_strtolower($definition['label']);
        }

        return $attributes;
    }
}
