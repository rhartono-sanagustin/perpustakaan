<?php

namespace App\Policies;

use App\Enums\LoanRequestStatus;
use App\Models\LoanRequest;
use App\Models\User;

class LoanRequestPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isActive();
    }

    public function view(User $user, LoanRequest $loanRequest): bool
    {
        return $user->isAdmin() || $loanRequest->user_id === $user->id;
    }

    public function create(User $user): bool
    {
        return $user->isActive() && ($user->isAdmin() || $user->isBorrower());
    }

    public function update(User $user, LoanRequest $loanRequest): bool
    {
        return $user->isAdmin();
    }

    public function delete(User $user, LoanRequest $loanRequest): bool
    {
        return false;
    }

    public function cancel(User $user, LoanRequest $loanRequest): bool
    {
        $ownsRequest = $user->isAdmin() || $loanRequest->user_id === $user->id;

        return $ownsRequest && $loanRequest->canBeCancelled();
    }

    public function approve(User $user, LoanRequest $loanRequest): bool
    {
        return $user->isAdmin() && $loanRequest->isPending();
    }

    public function reject(User $user, LoanRequest $loanRequest): bool
    {
        return $user->isAdmin() && $loanRequest->isPending();
    }

    public function checkout(User $user, LoanRequest $loanRequest): bool
    {
        return $user->isAdmin()
            && $loanRequest->status === LoanRequestStatus::Disetujui
            && ! $loanRequest->isExpired();
    }
}
