<?php

use App\User;
use App\Cart;
use App\Order;
use App\Product;
use App\Category;
use Faker\Generator as Faker;

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| Here you may define all of your model factories. Model factories give
| you a convenient way to create models for testing and seeding your
| database. Just tell the factory how a default model should look.
|
*/

/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(User::class, function (Faker $faker) {
    $password = bcrypt('secret');

    return [
        'name' => $faker->name,
        'email' => $faker->unique()->safeEmail,
        // 'password' => '$2y$10$TKh8H1.PfQx37YgCzwiKb.KjNyWgaHb9cbcoQgdIVFlYg7B77UdFm', // secret
        'password' => $password ?: $password, // secret
        'remember_token' => str_random(10),
        'verified' => $verified = $faker->randomElement([User::VERIFIED_USER, User::UNVERIFIED_USER]),
        'verification_token' => $verified  == User::VERIFIED_USER ? null : User::generateVerificationCode(),
        'admin' => $faker->randomElement([User::ADMIN_USER, User::REGULAR_USER])
    ];
});

$factory->define(Category::class, function (Faker $faker) {
    return [
        'name' => $faker->word,
        'description' => $faker->paragraph(1)
    ];
});


// Use this for unit testing
$factory->define(Product::class, function (Faker $faker) {

    return [
        'title' => $faker->word,
        'description' => $faker->paragraph(1),
        'quantity' => $faker->numberBetween(100, 1000),
        'status' => $faker->randomElement([Product::AVAILABLE_PRODUCT, Product::UNAVAILABLE_PRODUCT]),
        'price' => $faker->numberBetween(20.5, 9999),
        'image' => $faker->randomElement(['1.jpg', '2.jpg', '3.jpg']),
        // 'seller_id' => User::all()->where('admin', User::ADMIN_USER)->random()->id
        'seller_id' => function() {
            return factory('App\User')->create(['admin' => User::ADMIN_USER])->id;
        }
    ];
});

// Use this for unit testing
$factory->define(Cart::class, function (Faker $faker) {
    $product = Product::all()
        ->where('status', Product::AVAILABLE_PRODUCT)
        ->where('quantity', '!=', 0)
        ->random();

    $amount = $faker->numberBetween(1, 100);
    
    return [
        'buyer_id' => User::all()->where('admin', User::REGULAR_USER)->random()->id,
        'product_id' => $product->id,
        'price' => $product->price,
        'amount' => $amount,
        'total' => $product->price * $amount
    ];
});

// Use this for unit testing
$factory->define(Order::class, function (Faker $faker) {
    $cart = Cart::all()->random();

    $cart_total = Cart::with('Products')
        ->where('buyer_id', $cart->buyer_id)
        ->sum('total');

    return [
        'buyer_id' => $cart->buyer_id,
        'address' => $faker->paragraph(1),
        'total' => $cart_total,
        'status' => $faker->randomElement([Order::PENDING_ORDER, Order::INPROGRESS_ORDER, Order::COMPLETED_ORDER])
    ];
});





