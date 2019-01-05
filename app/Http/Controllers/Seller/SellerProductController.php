<?php

namespace App\Http\Controllers\Seller;

use App\User;
use App\Seller;
use App\Product;
use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;
use Illuminate\Support\Facades\Storage;
use App\Transformers\ProductTransformer;
use Illuminate\Auth\Access\AuthorizationException;
use Symfony\Component\HttpKernel\Exception\HttpException;

class SellerProductController extends ApiController
{
    public function __construct()
    {
        parent::__construct();

        $this->middleware('transform.input:' . ProductTransformer::class)->only(['store', 'update']);
        $this->middleware('scope:manage-products')->except(['index']);
        $this->middleware('can:view,seller')->only('index');
        $this->middleware('can:sell,seller')->only('store');
        $this->middleware('can:update,seller')->only('update');
        $this->middleware('can:delete,seller')->only('destroy');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Seller $seller)
    {
        if(request()->user()->tokenCan('read-general') || request()->user()->tokenCan('manage-products')) {
            $products = $seller->products;
            return $this->showAll($products);
        }

        throw new AuthorizationException('Invalid scope(s)');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\User $seller
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, User $seller)
    {
        $this->allowedAdminAction();

        $this->validate($request, [
            'title' => 'required|max:255',
            'description' => 'required|max:100',
            'quantity' => 'required|integer|min:1',
            'price' => 'required|integer',
            'image' => 'required|image',
        ]);

        $data = $request->all();

        $data['seller_id'] = $seller->id;

        $data['image'] = $request->image->store('');

        $data['status'] = Product::UNAVAILABLE_PRODUCT;

        $product = Product::create($data);

        return $this->showOne($product);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Seller  $seller
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Seller $seller, Product $product)
    {
        $rules = [
            'quantity' => 'integer|min:1',
            'image' => 'image',
            'price' => 'integer',
            'status' => 'in:' . Product::AVAILABLE_PRODUCT . ',' . Product::UNAVAILABLE_PRODUCT,
            'description' => 'max:1000',
        ];

        $this->validate($request, $rules);

        $this->checkSeller($seller, $product);

        $product->fill($request->only([
            'name',
            'description',
            'quantity',
            'status',
        ]));

        if($request->has('status')) {
            $product->status = $request->status;

            if($product->isAvailable() && $product->categories()->count() == 0) {
                return $this->errorResponse('An active product must have at least one category', 409);
            }
        }
        
        if($request->hasFile('image')) { 
            Storage::delete($product->image);
            
            $product->image = $request->image->store('');
        }

        if($product->isClean()) {
            return $this->errorResponse('You must specify different values', 422);
        }

        $product->save();

        return $this->showOne($product);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Seller  $seller, \App\Product $product
     * @return \Illuminate\Http\Response
     */
    public function destroy(Seller $seller, Product $product)
    {        
        $this->checkSeller($seller, $product);

        $product->delete();

        return $this->showOne($product);
    }

    /**
     * Check if the seller is the owner of the product
     * @param  $seller, $product
     * @return Symfony\Component\HttpKernel\Exception\HttpException;
     */
    protected function checkSeller($seller, $product)
    {
        if($seller->id != $product->seller_id) {
            throw new HttpException(422, 'The specified seller is not the actual seller of the product');
        }
    }
}
