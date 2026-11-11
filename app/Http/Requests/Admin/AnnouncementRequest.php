<?php

namespace App\Http\Requests\Admin;

use App\Enums\AnnouncementAudience;
use App\Models\Announcement;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AnnouncementRequest extends FormRequest
{
    public function authorize(): bool
    {
        /** @var Announcement|null $announcement */
        $announcement = $this->route('announcement');

        return $announcement !== null
            ? $this->user()?->can('update', $announcement) === true
            : $this->user()?->can('create', Announcement::class) === true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:150'],
            'body' => ['required', 'string', 'max:5000'],
            'audience' => ['required', Rule::enum(AnnouncementAudience::class)],
            'is_active' => ['nullable', 'boolean'],
            'published_at' => ['nullable', 'date'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'title' => 'judul',
            'body' => 'isi pengumuman',
            'audience' => 'audiens',
            'is_active' => 'status aktif',
            'published_at' => 'tanggal terbit',
        ];
    }
}
