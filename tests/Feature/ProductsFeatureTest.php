<?php

namespace Tests\Feature;

use App\User;
use Tests\TestCase;
use Laravel\Passport\Passport;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ProductsFeatureTest extends TestCase
{
    use DatabaseMigrations;

    protected $header = [];

    public function setUp()
    {
        parent::setUp();

        $this->artisan('passport:install');

        $this->user = create('App\User', ['verified' => User::VERIFIED_USER, 'admin' => User::ADMIN_USER]);

        $this->regularUser = create('App\User', ['verified' => User::VERIFIED_USER, 'admin' => User::REGULAR_USER]);

        $this->token = $this->user->createToken('TestToken', ['manage-products'])->accessToken;

        $this->header['Accept'] = 'application/json';

        $this->header['Authorization'] = 'Bearer '.$this->token;
    }

    /**
     * Guests with client credential can view all products.
     *
     * @return void
     */
    public function test_Guests_With_Client_Can_View_All_Products()
    {
        $this->withExceptionHandling();

        $product = create('App\Product', ['name' => "SIMPle Product"]);

        $product2 = create('App\Product', ['name' => "Simple pROduct2"]);

        $product3 = create('App\Product', ['name' => "SimpLE Product3"]);

        $data = [
            "error" => "Unauthenticated",
            "code" => 401
        ];

        $this->json('GET', 'api/products')
            ->assertJsonFragment($data)
            ->assertStatus(401);

        actingAsClient($this);

        $data = [
            'name' => "Simple Product",
            'name' => "Simple Product2",
            'name' => "Simple Product3",
        ];

        $this->json('GET', 'api/products')
            ->assertJsonFragment($data)
            ->assertStatus(200);
    }

    /**
     * products data validation.
     *
     * @return void
     */
    public function test_Products_Data_Validation()
    {
        $this->withExceptionHandling();

        $user = create('App\User', ['admin' => User::ADMIN_USER]);

        $input = [];

        $data = [
            "error" => [
                "name" => ["The name field is required."],
                "details" => ["The details field is required."],
                "stock" => ["The stock field is required."],
                "price" => ["The price field is required."],
                "image" => ["The image field is required."]
            ]
        ];

        $this->json('POST', 'api/sellers/'.$this->user->id.'/products', $input, $this->header)
            ->assertJsonFragment($data)
            ->assertStatus(422);
    }

    /**
     * Non admin cannot add product.
     *
     * @return void
     */
    public function test_Non_Admin_Cannot_Add_Product()
    {
        $this->withExceptionHandling();

        $this->token = $this->regularUser->createToken('TestToken2', ['manage-products'])->accessToken;

        $this->header['Authorization'] = 'Bearer '.$this->token;

        $data = [
            "error" => "This action is unauthorized.",
            "code" => 403
        ]; 

        $this->json('POST', 'api/sellers/'.$this->regularUser->id.'/products', [], $this->header)
            ->assertExactJson($data)
            ->assertStatus(403);
    }
}
