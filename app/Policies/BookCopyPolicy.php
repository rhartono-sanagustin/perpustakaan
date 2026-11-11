<?php

namespace App\Policies;

use App\Models\BookCopy;
use App\Models\User;

class BookCopyPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin();
    }

    public function view(User $user, BookCopy $copy): bool
    {
        return $user->isAdmin();
    }

    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    public function update(User $user, BookCopy $copy): bool
    {
        return $user->isAdmin();
    }

    public function delete(User $user, BookCopy $copy): bool
    {
        return $user->isAdmin();
    }
}
