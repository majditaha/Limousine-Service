<?php

namespace App\Policies;

use App\User;
use App\Theory;
use Illuminate\Auth\Access\HandlesAuthorization;

class TheoryPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view theories.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function index(User $user)
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can view the theory.
     *
     * @param  \App\User  $user
     * @param  \App\Theory  $theory
     * @return mixed
     */
    public function view(User $user, Theory $theory)
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can create theories.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can update the theory.
     *
     * @param  \App\User  $user
     * @param  \App\Theory  $theory
     * @return mixed
     */
    public function update(User $user, Theory $theory)
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can delete the theory.
     *
     * @param  \App\User  $user
     * @param  \App\Theory  $theory
     * @return mixed
     */
    public function delete(User $user, Theory $theory)
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can set theory as finished (add progress).
     *
     * @param  \App\User  $user
     * @param  \App\Theory  $theory
     * @return mixed
     */
    public function setFinished(User $user, Theory $theory)
    {
        if ($user->isAdmin()) {
            return true;
        }

        if (!$user->active) {
            return false;
        }

        $discipline = $theory->section->discipline;
        return $user->disciplines->contains($discipline);
    }
}
