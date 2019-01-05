<?php

namespace App\Policies;

use App\User;
use App\Traits\AdminActions;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization, AdminActions;

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\User  $user
     * @param  \App\User  $authUser
     * @return mixed
     */
    public function view(User $authUser, User $user)
    {
        return $authUser->id === $user->id;
    }
    
    /**
     * Determine whether the user can update the authUser.
     *
     * @param  \App\User  $user
     * @param  \App\User  $authUser
     * @return mixed
     */
    public function update(User $authUser, User $user)
    {
        return $authUser->id === $user->id;
    }

    /**
     * Determine whether the user can rate a movie.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function rate(User $authUser, User $user)
    {
        return $authUser->id === $user->id;
    }

    /**
     * Determine whether the user can delete the authUser.
     *
     * @param  \App\User  $user
     * @param  \App\User  $authUser
     * @return mixed
     */
    public function delete(User $authUser, User $user)
    {
        return $authUser->id === $user->id && $authUser->token()->client->personal_access_client;
    }
}
