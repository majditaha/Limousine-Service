<?php

namespace App\Policies;

use App\User;
use App\MenuItem;
use Illuminate\Auth\Access\HandlesAuthorization;

class MenuItemPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view menuItems.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function index(User $user)
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can view the menuItem.
     *
     * @param  \App\User  $user
     * @param  \App\MenuItem  $menuItem
     * @return mixed
     */
    public function view(User $user, MenuItem $menuItem)
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can create menuItems.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can update the menuItem.
     *
     * @param  \App\User  $user
     * @param  \App\MenuItem  $menuItem
     * @return mixed
     */
    public function update(User $user, MenuItem $menuItem)
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can delete the menuItem.
     *
     * @param  \App\User  $user
     * @param  \App\MenuItem  $menuItem
     * @return mixed
     */
    public function delete(User $user, MenuItem $menuItem)
    {
        return $user->isAdmin();
    }
}
