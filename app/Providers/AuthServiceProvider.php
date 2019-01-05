<?php

namespace App\Providers;

use App\User;
use App\Cart;
use App\Buyer;
use App\Seller;
use App\Product;
use Carbon\Carbon;
use App\Policies\CartPolicy;
use App\Policies\UserPolicy;
use App\Policies\BuyerPolicy;
use App\Policies\OrderPolicy;
use App\Policies\SellerPolicy;
use Laravel\Passport\Passport;
use App\Policies\ProductPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{

    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        User::class => UserPolicy::class,
        Cart::class => CartPolicy::class,
        Order::class => OrderPolicy::class,
        Buyer::class => BuyerPolicy::class,
        Seller::class => SellerPolicy::class,
        Product::class => ProductPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        Gate::define('admin-action', function ($user) {
            return $user->isAdmin();
        });

        Passport::routes();
        Passport::tokensExpireIn(Carbon::now()->addMinutes(60));
        Passport::refreshTokensExpireIn(Carbon::now()->addDays(30));
        Passport::enableImplicitGrant();
        Passport::tokensCan([
            'purchase-product' => 'Purchase a new product',
            'manage-products' => 'create, read, update, and delete products (CRUD)',
            'manage-orders' => 'read, update, and delete orders',
            'manage-account' => 'Read your account data, id, name, email, if verified, and if
                admin (cannot read password). Modify your account data (email, and password).
                cannot delete your account',
            'read-general' => 'Read general information like purchasing categories, 
                purchased products, selling products, selling categories, your transactions (purchases, carts and sales)',
        ]);
    }
}