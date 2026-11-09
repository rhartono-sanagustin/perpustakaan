<?php

namespace App\Policies;

use App\Models\Publisher;
use App\Models\User;

class PublisherPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin();
    }

    public function view(User $user, Publisher $publisher): bool
    {
        return $user->isAdmin();
    }

    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    public function update(User $user, Publisher $publisher): bool
    {
        return $user->isAdmin();
    }

    public function delete(User $user, Publisher $publisher): bool
    {
        return $user->isAdmin();
    }
}
