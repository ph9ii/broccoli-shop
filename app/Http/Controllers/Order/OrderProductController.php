<?php

namespace App\Http\Controllers\Order;

use App\Order;
use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;

class OrderProductController extends ApiController
{
    public function __construct()
    {
        parent::__construct();
        $this->middleware('scope:manage-orders')->only(['index']);
        $this->middleware('can:view,order')->only('index');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Order $order)
    {
        $products = $order->orderProducts;

        return $this->showAll($products);
    }

}
