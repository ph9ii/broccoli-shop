<?php

namespace App\Http\Controllers\Order;

use App\Order;
use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;

class OrderCategoryController extends ApiController
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
    public function index(Order $order)
    {
        $this->allowedAdminAction();
        
        $categories = $order->orderProducts()
            ->with('categories')
            ->whereHas('categories')
            ->get()
            ->pluck('categories')
            ->collapse();

        return $this->showAll($categories);
    }
}
