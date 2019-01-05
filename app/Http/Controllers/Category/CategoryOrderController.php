<?php

namespace App\Http\Controllers\Category;

use App\Category;
use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;

class CategoryOrderController extends ApiController
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
    public function index(Category $category)
    {
        $this->allowedAdminAction();
        
        $orders = $category->products()
            ->with('orders')
            ->whereHas('orders')
            ->get()
            ->pluck('orders')
            ->collapse();
        
        return $this->showAll($orders);
    }
}