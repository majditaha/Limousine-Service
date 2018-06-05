<?php

namespace App\Policies;

use App\User;
use App\Section;
use Illuminate\Auth\Access\HandlesAuthorization;

class SectionPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view sections.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function index(User $user)
    {
        if ($user->isAdmin()) {
            return true;
        }

        if (!$user->active) {
            return false;
        }

        if (request()->discipline) {
            return $user->disciplines->contains(request()->discipline);
        }
    }

    /**
     * Determine whether the user can view the section.
     *
     * @param  \App\User  $user
     * @param  \App\Section  $section
     * @return mixed
     */
    public function view(User $user, Section $section)
    {
        if ($user->isAdmin()) {
            return true;
        }

        if (!$user->active) {
            return false;
        }

        return $user->disciplines->contains($section->discipline);
    }

    /**
     * Determine whether the user can create sections.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can update the section.
     *
     * @param  \App\User  $user
     * @param  \App\Section  $section
     * @return mixed
     */
    public function update(User $user, Section $section)
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can delete the section.
     *
     * @param  \App\User  $user
     * @param  \App\Section  $section
     * @return mixed
     */
    public function delete(User $user, Section $section)
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can get training practices for the section.
     *
     * @param  \App\User  $user
     * @param  \App\Section  $section
     * @return mixed
     */
    public function getTrainings(User $user, Section $section)
    {
        if ($user->isAdmin()) {
            return true;
        }

        if (!$user->active) {
            return false;
        }

        return $user->disciplines->contains($section->discipline);
    }

    /**
     * Determine whether the user can set section as finished
     *
     * @param  \App\User  $user
     * @param  \App\Section  $section
     * @return mixed
     */
    public function setFinished(User $user, Section $section)
    {
        if ($user->isAdmin()) {
            return true;
        }

        if (!$user->active) {
            return false;
        }

        return $user->disciplines->contains($section->discipline) && $user->active;
    }

    /**
     * Determine whether the user can drop progress of a section
     *
     * @param  \App\User  $user
     * @param  \App\Section  $section
     * @return mixed
     */
    public function dropProgress(User $user, Section $section)
    {
        if ($user->isAdmin()) {
            return true;
        }

        if (!$user->active) {
            return false;
        }

        return $user->disciplines->contains($section->discipline) && $section->canPassAgain($user) && $user->active;
    }
}
