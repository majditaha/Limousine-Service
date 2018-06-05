<?php

namespace App\Policies;

use App\User;
use App\Subtype;
use Illuminate\Auth\Access\HandlesAuthorization;

class SubtypePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view subtypes.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function index(User $user)
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can view the subtype.
     *
     * @param  \App\User  $user
     * @param  \App\Subtype  $subtype
     * @return mixed
     */
    public function view(User $user, Subtype $subtype)
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can create subtypes.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can update the subtype.
     *
     * @param  \App\User  $user
     * @param  \App\Subtype  $subtype
     * @return mixed
     */
    public function update(User $user, Subtype $subtype)
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can delete the subtype.
     *
     * @param  \App\User  $user
     * @param  \App\Subtype  $subtype
     * @return mixed
     */
    public function delete(User $user, Subtype $subtype)
    {
        return $user->isAdmin();
    }
}
