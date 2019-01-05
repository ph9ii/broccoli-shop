<?php

use App\User;
use App\Cart;
use App\Order;
use App\Product;
use App\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;


class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // $this->call(UsersTableSeeder::class);
        DB::statement('SET FOREIGN_KEY_CHECKS = 0');

        Category::truncate();
        Product::truncate();
        Order::truncate();
        User::truncate();
        Cart::truncate();
        
        DB::table('translations')->truncate();
        DB::table('category_product')->truncate();
        DB::table('order_product')->truncate();

    
        $categoriesQuantity = 50;
        $usersQuantity      = 200;
        $productsQuantity   = 500;
        $cartsQuantity      = 800;
        $ordersQuantity     = 200;

        factory(User::class, $usersQuantity)->create();

        factory(Category::class, $categoriesQuantity)->create()->each(
            function ($category) {
                $category->translations()->create([
                    'language' => 'fr',
                    'content' => [
                        'name' => 'This Text translated as dummy fr'
                    ],
                ]);

                $category->translations()->create([
                    'language' => 'de',
                    'content' => [
                        'name' => 'This Text translated as dummy de'
                    ],
                ]);
            }
        );

        factory(Product::class, $productsQuantity)->create()->each(
        	function ($product) {
        		$categories = Category::all()->random(mt_rand(1, 5))
                    ->pluck('id');

        		$product->categories()->attach($categories);

                $product->translations()->create([
                    'language' => 'de',
                    'content' => [
                        'description' => 'This Text translated as dummy de'
                    ],
                ]);

                $product->translations()->create([
                    'language' => 'fr',
                    'content' => [
                        'description' => 'This Text translated as dummy fr'
                    ],
                ]);
        	});

        factory(Cart::class, $cartsQuantity)->create();

        factory(Order::class, $ordersQuantity)->create()->each(
            function ($order) {

                $carts = Cart::all()
                    ->where('buyer_id', $order->buyer_id);

                foreach ($carts as $cart) {
                    $order->orderProducts()->attach($cart->product_id, [
                      'amount'=> $cart->amount,
                      'price'=> $cart->price,
                      'total'=> $cart->total,
                      'created_at' => $cart->created_at,
                      'updated_at' => $cart->updated_at
                    ]);
                }

                Cart::where('buyer_id', $order->buyer_id)->delete();
        });
    }
}
