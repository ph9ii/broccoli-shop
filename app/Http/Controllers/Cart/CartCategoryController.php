<?php

namespace App\Http\Controllers\Cart;

use App\Cart;
use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;

class CartCategoryController extends ApiController
{
    public function __construct()
    {
        parent::__construct();
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Cart $cart)
    {
        $this->allowedAdminAction();

        $categories = $cart->product->categories;

        return $this->showAll($categories);
    }
}
