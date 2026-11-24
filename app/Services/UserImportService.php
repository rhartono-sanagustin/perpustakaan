<?php

namespace App\Services;

use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Models\User;
use App\Support\ImportReport;
use App\Support\SpreadsheetData;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class UserImportService
{
    /** @var array<string, list<string>> */
    private const REQUIRED_HEADERS = [
        'Nama' => ['Nama', 'Nama Lengkap'],
        'Nomor Induk' => ['Nomor Induk', 'NIS', 'NIP', 'NIK'],
        'Peran' => ['Peran', 'Role'],
    ];

    public function __construct(
        private readonly SpreadsheetReader $reader,
        private readonly AuditLogger $auditLogger,
    ) {}

    public function import(UploadedFile $file, User $actor): ImportReport
    {
        $data = $this->reader->read($file);
        $missing = $this->missingHeaders($data);

        if ($missing !== []) {
            throw new InvalidArgumentException(
                'Kolom wajib tidak ditemukan: '.implode(', ', $missing).'.'
            );
        }

        $report = new ImportReport(mb_substr($file->getClientOriginalName(), 0, 255));
        $seenNomorInduk = [];
        $seenEmail = [];

        DB::transaction(function () use ($data, $actor, $report, &$seenNomorInduk, &$seenEmail): void {
            foreach ($data->rows as $row) {
                $line = $row['line'];

                try {
                    $payload = $this->payload($data, $row['values']);
                    $this->validate($payload, $seenNomorInduk, $seenEmail);

                    $user = User::query()
                        ->where('nomor_induk', $payload['nomor_induk'])
                        ->first();

                    if ($user !== null && $actor->is($user)) {
                        throw new InvalidArgumentException(
                            'Akun yang sedang digunakan tidak dapat diubah melalui impor.'
                        );
                    }

                    $this->assertEmailAvailable($payload['email'], $user);

                    if ($user === null) {
                        if ($payload['password'] === '') {
                            throw new InvalidArgumentException(
                                'Kata sandi wajib diisi untuk akun baru (minimal 8 karakter).'
                            );
                        }

                        User::query()->create($this->createAttributes($payload));
                        $report->markCreated();
                    } else {
                        $user->update($this->updateAttributes($payload));
                        $report->markUpdated();
                    }

                    $seenNomorInduk[$payload['nomor_induk']] = true;

                    if ($payload['email'] !== '') {
                        $seenEmail[mb_strtolower($payload['email'])] = true;
                    }
                } catch (InvalidArgumentException $exception) {
                    $report->markFailed($line, $exception->getMessage());
                }
            }
        });

        $this->auditLogger->recordEvent('user.imported', User::class, null, null, [
            'filename' => $report->filename,
            'created' => $report->created,
            'updated' => $report->updated,
            'failed' => $report->failed,
        ]);

        return $report;
    }

    private function missingHeaders(SpreadsheetData $data): array
    {
        $missing = [];

        foreach (self::REQUIRED_HEADERS as $label => $aliases) {
            if (! $data->hasAny($aliases)) {
                $missing[] = $label;
            }
        }

        return $missing;
    }

    /**
     * @param  list<string>  $values
     * @return array{
     *     name: string,
     *     nomor_induk: string,
     *     email: string,
     *     role: UserRole,
     *     status: UserStatus|null,
     *     kelas: string,
     *     unit_kerja: string,
     *     no_telepon: string,
     *     password: string
     * }
     */
    private function payload(SpreadsheetData $data, array $values): array
    {
        return [
            'name' => $data->value($values, ['Nama', 'Nama Lengkap']),
            'nomor_induk' => $data->value($values, ['Nomor Induk', 'NIS', 'NIP', 'NIK']),
            'email' => $data->value($values, ['Email', 'E-mail']),
            'role' => $this->parseRole($data->value($values, ['Peran', 'Role'])),
            'status' => $this->parseStatus($data->value($values, ['Status', 'Status Akun'])),
            'kelas' => $data->value($values, ['Kelas']),
            'unit_kerja' => $data->value($values, ['Unit Kerja', 'Unit']),
            'no_telepon' => $data->value($values, ['Nomor Telepon', 'No. Telepon', 'Telepon']),
            'password' => $data->value($values, ['Kata Sandi', 'Password']),
        ];
    }

    /**
     * @param  array<string, mixed>  $payload
     * @param  array<string, bool>  $seenNomorInduk
     * @param  array<string, bool>  $seenEmail
     */
    private function validate(array $payload, array $seenNomorInduk, array $seenEmail): void
    {
        if ($payload['name'] === '') {
            throw new InvalidArgumentException('Nama wajib diisi.');
        }

        if (mb_strlen($payload['name']) > 255) {
            throw new InvalidArgumentException('Nama maksimal 255 karakter.');
        }

        if ($payload['nomor_induk'] === '') {
            throw new InvalidArgumentException('Nomor induk wajib diisi.');
        }

        if (mb_strlen($payload['nomor_induk']) > 50) {
            throw new InvalidArgumentException('Nomor induk maksimal 50 karakter.');
        }

        if (isset($seenNomorInduk[$payload['nomor_induk']])) {
            throw new InvalidArgumentException(
                'Nomor induk "'.$payload['nomor_induk'].'" muncul lebih dari sekali di berkas.'
            );
        }

        if ($payload['email'] !== '') {
            if (mb_strlen($payload['email']) > 255) {
                throw new InvalidArgumentException('Email maksimal 255 karakter.');
            }

            if (filter_var($payload['email'], FILTER_VALIDATE_EMAIL) === false) {
                throw new InvalidArgumentException('Format email tidak valid.');
            }

            if (isset($seenEmail[mb_strtolower($payload['email'])])) {
                throw new InvalidArgumentException('Email muncul lebih dari sekali di berkas.');
            }
        }

        if (mb_strlen((string) $payload['kelas']) > 50) {
            throw new InvalidArgumentException('Kelas maksimal 50 karakter.');
        }

        if (mb_strlen((string) $payload['unit_kerja']) > 100) {
            throw new InvalidArgumentException('Unit kerja maksimal 100 karakter.');
        }

        if (mb_strlen((string) $payload['no_telepon']) > 30) {
            throw new InvalidArgumentException('Nomor telepon maksimal 30 karakter.');
        }

        if ($payload['password'] !== '' && mb_strlen($payload['password']) < 8) {
            throw new InvalidArgumentException('Kata sandi minimal 8 karakter.');
        }
    }

    private function parseRole(string $value): UserRole
    {
        $normalized = mb_strtolower(trim($value));

        if ($normalized === '') {
            throw new InvalidArgumentException('Peran wajib diisi (admin, guru, atau siswa).');
        }

        foreach (UserRole::cases() as $role) {
            if ($normalized === $role->value || $normalized === mb_strtolower($role->label())) {
                return $role;
            }
        }

        if ($normalized === 'administrator') {
            return UserRole::Admin;
        }

        throw new InvalidArgumentException(
            'Peran "'.$value.'" tidak dikenal. Gunakan admin, guru, atau siswa.'
        );
    }

    private function parseStatus(string $value): ?UserStatus
    {
        $normalized = mb_strtolower(trim($value));

        return match ($normalized) {
            '' => null,
            'aktif', 'active', '1', 'ya', 'true' => UserStatus::Aktif,
            'nonaktif', 'non-aktif', 'inactive', '0', 'tidak', 'false' => UserStatus::Nonaktif,
            default => throw new InvalidArgumentException(
                'Status "'.$value.'" tidak dikenal. Gunakan aktif atau nonaktif.'
            ),
        };
    }

    private function assertEmailAvailable(string $email, ?User $user): void
    {
        if ($email === '') {
            return;
        }

        $query = User::query()->where('email', $email);

        if ($user !== null) {
            $query->whereKeyNot($user->getKey());
        }

        if ($query->exists()) {
            throw new InvalidArgumentException('Email sudah digunakan pengguna lain.');
        }
    }

    /**
     * @param  array<string, mixed>  $payload
     * @return array<string, mixed>
     */
    private function createAttributes(array $payload): array
    {
        return [
            'name' => $payload['name'],
            'email' => $payload['email'] !== '' ? $payload['email'] : null,
            'nomor_induk' => $payload['nomor_induk'],
            'role' => $payload['role'],
            'status' => $payload['status'] ?? UserStatus::Aktif,
            'kelas' => $payload['kelas'] !== '' ? $payload['kelas'] : null,
            'unit_kerja' => $payload['unit_kerja'] !== '' ? $payload['unit_kerja'] : null,
            'no_telepon' => $payload['no_telepon'] !== '' ? $payload['no_telepon'] : null,
            'password' => $payload['password'],
        ];
    }

    /**
     * @param  array<string, mixed>  $payload
     * @return array<string, mixed>
     */
    private function updateAttributes(array $payload): array
    {
        $attributes = [
            'name' => $payload['name'],
            'role' => $payload['role'],
        ];

        foreach (['email', 'kelas', 'unit_kerja', 'no_telepon'] as $field) {
            if ($payload[$field] !== '') {
                $attributes[$field] = $payload[$field];
            }
        }

        if ($payload['status'] !== null) {
            $attributes['status'] = $payload['status'];
        }

        if ($payload['password'] !== '') {
            $attributes['password'] = $payload['password'];
        }

        return $attributes;
    }
}
