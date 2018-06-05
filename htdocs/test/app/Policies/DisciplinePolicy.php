<?php

namespace App\Policies;

use App\User;
use App\Discipline;
use Illuminate\Auth\Access\HandlesAuthorization;

class DisciplinePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view disciplines.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function index(User $user)
    {
        return true;
    }

    /**
     * Determine whether the user can view the discipline.
     *
     * @param  \App\User  $user
     * @param  \App\Discipline  $discipline
     * @return mixed
     */
    public function view(User $user, Discipline $discipline)
    {
        if ($user->isAdmin()) {
            return true;
        }

        if (!$user->active) {
            return false;
        }

        return $user->disciplines->contains($discipline);
    }

    /**
     * Determine whether the user can create disciplines.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can update the discipline.
     *
     * @param  \App\User  $user
     * @param  \App\Discipline  $discipline
     * @return mixed
     */
    public function update(User $user, Discipline $discipline)
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can delete the discipline.
     *
     * @param  \App\User  $user
     * @param  \App\Discipline  $discipline
     * @return mixed
     */
    public function delete(User $user, Discipline $discipline)
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can get latest section in trainings
     *
     * @param  \App\User  $user
     * @param  \App\Discipline  $discipline
     * @return mixed
     */
    public function getLatestSectionInTraining(User $user, Discipline $discipline)
    {
        if ($user->isAdmin()) {
            return true;
        }

        if (!$user->active) {
            return false;
        }

        return $user->disciplines->contains($discipline);
    }
}
