<?php

namespace App\Policies;

use App\Models\Loan;
use App\Models\User;

class LoanPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isActive();
    }

    public function view(User $user, Loan $loan): bool
    {
        return $user->isAdmin() || $loan->user_id === $user->id;
    }

    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    public function update(User $user, Loan $loan): bool
    {
        return $user->isAdmin();
    }

    public function delete(User $user, Loan $loan): bool
    {
        return false;
    }

    public function markReturned(User $user, Loan $loan): bool
    {
        return $user->isAdmin() && $loan->isActive();
    }
}
