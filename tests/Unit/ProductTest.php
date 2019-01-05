<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ProductTest extends TestCase
{
	use DatabaseMigrations;

    protected $product;

    public function setUp()
    {
        parent::setUp();

        $this->product = create('App\Product');
    }

    /**
     * A product has categories.
     *
     * @return void
     */
    public function test_product_has_categories()
    {
        $this->assertInstanceOf('Illuminate\Database\Eloquent\Collection', $this->product->categories);
    }

    /**
     * A product has orders.
     *
     * @return void
     */
    public function test_product_has_orders()
    {
        $this->assertInstanceOf('Illuminate\Database\Eloquent\Collection', $this->product->orders);
    }

    /**
     * A product has carts.
     *
     * @return void
     */
    public function test_product_has_carts()
    {
        $this->assertInstanceOf('Illuminate\Database\Eloquent\Collection', $this->product->carts);
    }

    /**
     * A product has seller.
     *
     * @return void
     */
    public function test_product_belongs_to_seller()
    {
        $this->assertInstanceOf('App\Seller', $this->product->seller);
    }

    /**
     * Product add to cart.
     *
     * @return void
     */
    public function test_product_add_to_cart()
    {
        $amount = 10;
        $total = $this->product->price * $amount;

        $this->product->addToCart([
            'buyer_id' => 1,
            'product_id' => $this->product->id,
            'amount' => $amount,
            'price' => $this->product->price,
            'total' => $total
        ]);
    }
}
