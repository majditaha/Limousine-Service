<?php

namespace App\Policies;

use App\User;
use App\Variant;
use Illuminate\Auth\Access\HandlesAuthorization;

class VariantPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view variants.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function index(User $user)
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can view the variant.
     *
     * @param  \App\User  $user
     * @param  \App\Variant  $variant
     * @return mixed
     */
    public function view(User $user, Variant $variant)
    {
        if ($user->isAdmin()) {
            return true;
        }
        return $user->disciplines->contains($variant->discipline);
    }

    /**
     * Determine whether the user can create variants.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can update the variant.
     *
     * @param  \App\User  $user
     * @param  \App\Variant  $variant
     * @return mixed
     */
    public function update(User $user, Variant $variant)
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can delete the variant.
     *
     * @param  \App\User  $user
     * @param  \App\Variant  $variant
     * @return mixed
     */
    public function delete(User $user, Variant $variant)
    {
        return $user->isAdmin();
    }
}
