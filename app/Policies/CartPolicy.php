<?php

namespace App\Policies;

use App\Cart;
use App\User;
use App\Traits\AdminActions;
use Illuminate\Auth\Access\HandlesAuthorization;

class CartPolicy
{
    use HandlesAuthorization, AdminActions;
    
    /**
     * Determine whether the user can view the cart.
     *
     * @param  \App\User  $user
     * @param  \App\Cart  $cart
     * @return mixed
     */
    public function view(User $user, Cart $cart)
    {
        return $user->id === $cart->buyer->id || $user->id === $cart->product->seller->id;
    }   
}