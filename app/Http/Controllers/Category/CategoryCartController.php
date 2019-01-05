<?php

namespace App\Http\Controllers\Category;

use App\Category;
use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;

class CategoryCartController extends ApiController
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
        
        $carts = $category->products()->with('carts')
                    ->whereHas('carts')
                    ->get()
                    ->pluck('carts')
                    ->collapse();
        
        return $this->showAll($carts);
    }
}