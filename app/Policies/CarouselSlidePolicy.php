<?php

namespace App\Policies;

use App\Models\CarouselSlide;
use App\Models\User;

class CarouselSlidePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin();
    }

    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    public function update(User $user, CarouselSlide $slide): bool
    {
        return $user->isAdmin();
    }

    public function delete(User $user, CarouselSlide $slide): bool
    {
        return $user->isAdmin();
    }

    public function toggle(User $user, CarouselSlide $slide): bool
    {
        return $user->isAdmin();
    }

    public function move(User $user, CarouselSlide $slide): bool
    {
        return $user->isAdmin();
    }
}
