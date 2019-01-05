<?php

namespace App\Providers;

use Mail;
use App\Cart;
use App\User;
use App\Buyer;
use App\Order;
use App\Product;
use App\Mail\UserCreated;
use App\Mail\UserMailChanged;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {   
        
        Schema::defaultStringLength(191);

        //Listen to new user created event
        User::created(function($user) {
            retry(5, function() use ($user) {
                Mail::to($user)->send(new UserCreated($user));
            }, 100);
        });

        //Listen to user email updated event
        User::updated(function($user) {
            if($user->isDirty('email')) {
                retry(5, function() use ($user) {
                    Mail::to($user)->send(new UserMailChanged($user));
                }, 100);
            }
        });

        //Listen to user deleted event
        User::deleting(function($user) {
            $attr = ['buyer_id' => $user->id];
            $cart = new Cart;
            $carts = $cart->where($attr);
            if($carts->exists()) {
                $allCarts = $cart->get();
                foreach($allCarts as $cart) {
                    $cart->product->quantity += $cart->amount;
                    $cart->product->save();
                }
                $carts->delete();
            }
            $order = new Order;
            $orders = $order->where($attr);
            if($orders->exists()) {
                $orders->delete();
            }
        });

        //Listen to order deleted event
        // Order::deleting(function($order) {
        //     $order->orderProducts()->detach();
        // });

        //Listen to product updated event
        Product::updated(function($product) {
            if($product->quantity == 0 && $product->isAvailable()) {
                $product->status = Product::UNAVAILABLE_PRODUCT;
                $product->save();
            }
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}