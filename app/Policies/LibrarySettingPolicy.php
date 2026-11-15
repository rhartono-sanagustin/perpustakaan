<?php

namespace App\Policies;

use App\Models\LibrarySetting;
use App\Models\User;

class LibrarySettingPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin();
    }

    public function update(User $user, LibrarySetting $setting): bool
    {
        return $user->isAdmin();
    }
}
