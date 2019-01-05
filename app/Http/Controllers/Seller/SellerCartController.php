<?php

namespace App\Http\Controllers\Seller;

use App\Seller;
use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;

class SellerCartController extends ApiController
{
    public function __construct()
    {
        parent::__construct();
        $this->middleware('scope:manage-orders')->only(['index']);
        $this->middleware('can:view,seller')->only('index');
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Seller $seller)
    {
        $carts = $seller->products()
          ->whereHas('carts')
          ->with('carts')
          ->get()
          ->pluck('carts')
          ->collapse()
          ->unique('id')
          ->values();

        return $this->showAll($carts);
    }
  }