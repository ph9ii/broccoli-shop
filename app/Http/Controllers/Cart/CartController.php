<?php

namespace App\Http\Controllers\Cart;

use App\Cart;
use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;

class CartController extends ApiController
{
    public function __construct()
    {
        parent::__construct();
        $this->middleware('scope:manage-account')->only(['show']);
        $this->middleware('can:view,cart')->only('show');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->allowedAdminAction();
        
        $carts = Cart::all();

        return $this->showAll($carts);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Cart  $cart
     * @return \Illuminate\Http\Response
     */
    public function show(Cart $cart)
    {
        return $this->showOne($cart);
    }
}
