<?php

namespace App\Policies;

use App\Models\Book;
use App\Models\User;

class BookPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin();
    }

    public function view(User $user, Book $book): bool
    {
        return $user->isAdmin() || $book->is_active;
    }

    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    public function update(User $user, Book $book): bool
    {
        return $user->isAdmin();
    }

    public function delete(User $user, Book $book): bool
    {
        return $user->isAdmin();
    }

    public function archive(User $user, Book $book): bool
    {
        return $user->isAdmin();
    }

    public function activate(User $user, Book $book): bool
    {
        return $user->isAdmin();
    }
}
