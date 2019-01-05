<?php

namespace App\Policies;

use App\User;
use App\Order;
use App\Traits\AdminActions;
use Illuminate\Auth\Access\HandlesAuthorization;

class OrderPolicy
{
    use HandlesAuthorization, AdminActions;
    
    /**
     * Determine whether the user can view the order.
     *
     * @param  \App\User  $user
     * @param  \App\Order  $order
     * @return mixed
     */
    public function view(User $user, Order $order)
    {
        return $user->id === $order->buyer->id;
    }   
}