<?php

namespace App\Policies;

use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view users.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function index(User $user)
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the currentUser can view the user.
     *
     * @param  \App\User  $currentUser
     * @param  \App\User  $user
     * @return mixed
     */
    public function view(User $currentUser, User $user)
    {
        return $currentUser->isAdmin();
    }

    /**
     * Determine whether the currentUser can create users.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function create(User $currentUser)
    {
        return $currentUser->isAdmin();
    }

    /**
     * Determine whether the currentUser can update the user.
     *
     * @param  \App\User  $currentUser
     * @param  \App\User  $user
     * @return mixed
     */
    public function update(User $currentUser, User $user)
    {
        return $currentUser->isAdmin();
    }

    /**
     * Determine whether the currentUser can delete the user.
     *
     * @param  \App\User  $currentUser
     * @param  \App\User  $user
     * @return mixed
     */
    public function delete(User $currentUser, User $user)
    {
        return $currentUser->isAdmin();
    }
}
