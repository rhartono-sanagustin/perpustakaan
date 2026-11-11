<?php

namespace App\Services;

use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class AuditLogger
{
    /**
     * Catat tindakan penting beserta nilai sebelum dan sesudah perubahan.
     *
     * @param  array<string, mixed>|null  $oldValues
     * @param  array<string, mixed>|null  $newValues
     */
    public function record(string $action, Model $entity, ?array $oldValues = null, ?array $newValues = null): AuditLog
    {
        $request = request();
        $userAgent = $request->userAgent();

        return AuditLog::query()->create([
            'actor_id' => Auth::id(),
            'action' => $action,
            'entity_type' => $entity->getMorphClass(),
            'entity_id' => $entity->getKey(),
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'ip_address' => $request->ip(),
            'user_agent' => $userAgent !== null ? mb_substr($userAgent, 0, 500) : null,
        ]);
    }

    /**
     * Catat hanya atribut yang benar-benar berubah.
     *
     * @param  array<string, mixed>  $before
     * @param  array<string, mixed>  $after
     */
    public function recordChanges(string $action, Model $entity, array $before, array $after): ?AuditLog
    {
        $oldValues = [];
        $newValues = [];

        foreach ($after as $key => $value) {
            if (! array_key_exists($key, $before) || $before[$key] !== $value) {
                $oldValues[$key] = $before[$key] ?? null;
                $newValues[$key] = $value;
            }
        }

        foreach ($before as $key => $value) {
            if (! array_key_exists($key, $after)) {
                $oldValues[$key] = $value;
                $newValues[$key] = null;
            }
        }

        if ($oldValues === [] && $newValues === []) {
            return null;
        }

        return $this->record($action, $entity, $oldValues, $newValues);
    }
}
