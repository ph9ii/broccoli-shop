<?php

namespace Tests\Feature;

use App\User;
use App\Product;
use Tests\TestCase;
use Laravel\Passport\Passport;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class BuyersFeatureTest extends TestCase
{
    use DatabaseMigrations;

    protected $user;
    protected $product;
    protected $anotherUser;
    protected $productPrice, $productPriceToString;
    protected $header = [];

    public function setUp()
    {
        parent::setUp();

        $this->artisan('passport:install');

        $this->user = create('App\User', ['verified' => User::VERIFIED_USER, 'admin' => User::REGULAR_USER]);

        $this->anotherUser = create('App\User', ['verified' => User::VERIFIED_USER, 'admin' => User::REGULAR_USER]);

        $this->product = create('App\Product', ['status' => Product::AVAILABLE_PRODUCT]);

        $this->token = $this->user->createToken('TestToken', ['purchase-product'])->accessToken;

        $this->header['Accept'] = 'application/json';

        $this->header['Authorization'] = 'Bearer '.$this->token;

        $this->productPrice = $this->product->price;
        $this->productPriceToString = (string)$this->productPrice;
    }

    /**
     * Guests can not add products to their cart.
     *
     * @return void
     */
    public function test_Guests_Can_not_Add_Products_ToCart()
    {
        $this->withExceptionHandling();

        $data = [
            "error" => "Unauthenticated",
            "code" => 401
        ];

        $this->json('POST', "api/products/".$this->product->id."/buyers/".$this->user->id."/adds")
            ->assertExactJson($data)
            ->assertStatus(401);
    }

    /**
     * Auth users can add products to their cart only.
     *
     * @return void
     */
    public function test_Auth_Users_Can_Add_Products_To_Their_Cart_Only()
    {
        $this->withExceptionHandling();

        $input = ['quantity' => 1];

        // productPrice = $this->product->price;

        // $productPriceToString = (string)productPrice;

        $data = [
            "quantity" => 1,
            "buyerID" => $this->user->id,
            "productID" => $this->product->id,
            "price" => (string) number_format($this->productPriceToString),
            "sumTotal" => (string) number_format($this->productPriceToString, 2)
        ];

        $this->json('POST', 'api/products/'.$this->product->id.'/buyers/'.$this->user->id.'/adds', $input, $this->header)
            ->assertJsonFragment($data)
            ->assertStatus(201);

        $data = [
            "id" => 1,
            "buyer_id" =>  1,
            "product_id" => 1,
            "amount" => 1,
            "price" => $this->productPrice,
            "total" => $this->productPrice,
        ];

        $this->assertDatabaseHas('carts', $data);

        // Lets test with another user id

        $data = [
            "error" => "This action is unauthorized.",
            "code" => 403
        ];        

        $this->json('POST', 'api/products/'.$this->product->id.'/buyers/'.$this->anotherUser->id.'/adds', $input, $this->header)
            ->assertExactJson($data)
            ->assertStatus(403);
    }

    /**
     * Auth users can remove products from their cart only.
     *
     * @return void
     */
    public function test_Auth_Users_Can_Remove_Products_From_Their_Cart_Only()
    {
        $this->withExceptionHandling();

        $cart = create('App\Cart', [
            'buyer_id' => $this->user->id, 
            'product_id' => $this->product->id,
        ]);

        $this->json('DELETE', 'api/products/'.$this->product->id.'/buyers/'.$this->user->id.'/adds', [], $this->header)
            ->assertStatus(200);

        $data = [
            "buyer_id" =>  $this->user->id,
            "product_id" => $this->product->id
        ];

        $this->assertDatabaseMissing('carts', $data);

        // Lets test with another user id

        $data = [
            "error" => "This action is unauthorized.",
            "code" => 403
        ];        

        $this->json('DELETE', 'api/products/'.$this->product->id.'/buyers/'.$this->anotherUser->id.'/adds', [], $this->header)
            ->assertExactJson($data)
            ->assertStatus(403);
    }

    /**
     * Auth user can add quantity to product in his cart (Cannot add product twice).
     *
     * @return void
     */
    public function test_Auth_User_Can_Add_Quantity_To_Product_In_His_Cart()
    {
        $this->withExceptionHandling();

        $amount = 10;
        $total = $amount * $this->productPrice;

        $cart = create('App\Cart', [
            'buyer_id' => $this->user->id, 
            'product_id' => $this->product->id,
            'amount' => $amount,
            'total' => $total
        ]);

        // Lets now test adding same product again to cart

        $qty = 11;
        $sumTotalToString = (string) ($this->productPrice * $qty);

        $data = [
            "quantity" => $qty,
            "buyerID" => $this->user->id,
            "productID" => $this->product->id,
            "price" => (string) number_format($this->productPriceToString),
            "sumTotal" => (string) number_format($sumTotalToString, 2)
        ];

        $this->json('POST', 'api/products/'.$this->product->id.'/buyers/'.$this->user->id.'/adds', ['quantity' => 1], $this->header)
            ->assertJsonFragment($data)
            ->assertStatus(201);
    }

    /**
     * Auth users can submit order for his account only.
     *
     * @return void
     */
    public function test_Auth_User_Can_Submit_Order_For_His_Account_Only()
    {
        $this->withExceptionHandling();

        $amount = 10;

        $total = $amount * $this->product->price;

        $cart = create('App\Cart', [
            'buyer_id' => $this->user->id, 
            'product_id' => $this->product->id,
            'amount' => $amount,
            'total' => $total
        ]);

        $input = ['address' => "10, 32 buildings"];

        $data = [
            "buyerID" => $this->user->id,
            "orderStatus" => "pending"
        ];

        $this->json('POST', 'api/buyers/'.$this->user->id.'/transactions', $input, $this->header)
            ->assertJsonFragment($data)
            ->assertStatus(201);

        $data = [
            "order_id" => 1,
            "product_id" => $this->user->id,
            "amount" => $amount,
            "price" => $this->product->price,
            "total" => $total
        ];

        $this->assertDatabaseHas('order_product', $data);

        // Lets test with another user id

        $data = [
            "error" => "This action is unauthorized.",
            "code" => 403
        ];

        $this->json('POST', 'api/buyers/'.$this->anotherUser->id.'/transactions', $input, $this->header)
            ->assertExactJson($data)
            ->assertStatus(403);
    }
}
