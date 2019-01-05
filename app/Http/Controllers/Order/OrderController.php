<?php

namespace App\Http\Controllers\Order;

use App\Order;
use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;

class OrderController extends ApiController
{
    public function __construct()
    {
        parent::__construct();
        $this->middleware('scope:manage-orders')->only(['show']);
        $this->middleware('can:view,order')->only('show');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->allowedAdminAction();
        
        $orders = Order::all();

        return $this->showAll($orders);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function show(Order $order)
    {
        return $this->showOne($order);
    }
}
