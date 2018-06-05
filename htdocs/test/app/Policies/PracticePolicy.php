<?php

namespace App\Policies;

use App\User;
use App\Practice;
use Illuminate\Auth\Access\HandlesAuthorization;

class PracticePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view practices.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function index(User $user)
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can view the practice.
     *
     * @param  \App\User  $user
     * @param  \App\Practice  $practice
     * @return mixed
     */
    public function view(User $user, Practice $practice)
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can create practices.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can update the practice.
     *
     * @param  \App\User  $user
     * @param  \App\Practice  $practice
     * @return mixed
     */
    public function update(User $user, Practice $practice)
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can delete the practice.
     *
     * @param  \App\User  $user
     * @param  \App\Practice  $practice
     * @return mixed
     */
    public function delete(User $user, Practice $practice)
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can set practice as finished (add progress).
     *
     * @param  \App\User  $user
     * @param  \App\Practice  $practice
     * @return mixed
     */
    public function setFinished(User $user, Practice $practice)
    {
        if ($user->isAdmin()) {
            return true;
        }

        if (!$user->active) {
            return false;
        }

        if ($practice->discipline_id) {
            $discipline = $practice->discipline;
        }
        else if ($practice->section_id) {
            $discipline = $practice->section->discipline;
        }
        else if ($practice->theory_id) {
            $discipline = $practice->theory->section->discipline;
        }
        return $user->disciplines->contains($discipline);
    }

    /**
     * Determine whether the user can get practices by smart algorithm
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function getSmart(User $user)
    {
        return $user->active;
    }
}
