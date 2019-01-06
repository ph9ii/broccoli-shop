<?php

namespace App\Http\Controllers\Buyer;

use App\Buyer;
use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;

class BuyerProductController extends ApiController
{
    public function __construct()
    {
        parent::__construct();
        $this->middleware('scope:manage-account')->only(['index']);
        $this->middleware('can:view,buyer')->only('index');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Buyer $buyer)
    {
        $products = $buyer->orders()->with('orderProducts.seller.products')
            ->get()
            ->pluck('orderProducts')
            ->collapse()
            ->pluck('seller') 
            ->pluck('products')
            ->collapse()
            ->unique('id')
            ->values();

        return $this->showAll($products);
    }
}