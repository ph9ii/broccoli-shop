<?php

namespace App\Http\Controllers\Cart;

use App\Cart;
use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;

class CartSellerController extends ApiController
{
    public function __construct()
    {
        parent::__construct();
        $this->middleware('scope:manage-orders')->only(['index']);
        $this->middleware('can:view,cart')->only('index');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Cart $cart)
    {
        $this->allowedAdminAction();
        
        $seller = $cart->product->seller;

        return $this->showOne($seller);
    }

}
