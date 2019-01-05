<?php

namespace App\Http\Controllers\Product;

use App\Buyer;
use App\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Transformers\CartTransformer;
use App\Http\Controllers\ApiController;

class ProductBuyerAddToCartController extends ApiController
{
    public function __construct()
    {
      parent::__construct();

      $this->middleware('transform.input:' . CartTransformer::class)->only(['store']);
      $this->middleware('scope:purchase-product')->only(['store', 'removeFromCart']);
      $this->middleware('can:purchase,buyer')->only('store', 'removeFromCart');
    }

    /**
      * Store a newly created resource in storage.
      *
      * @param  \App\Buyer  $buyer
      * @param  \App\Product  $product
      * @param  \Illuminate\Http\Request  $request
      * @return \Illuminate\Http\Response 
      */
    public function store(Request $request, Product $product, Buyer $buyer)
    {
      $rules = [
        'amount' => 'required|integer|min:1',
      ];

      $this->validate($request, $rules);

      if($product->seller_id == $buyer->id) {
        return $this->errorResponse('Buyer must be different from the seller', 409);
      }

      if($product->quantity < $request->quantity) {
        return $this->errorResponse('Purchase quantity is larger than the product quantity', 409);
      }

      if(!$buyer->isVerified()) {
        return $this->errorResponse('Buyer is not verified', 409);
      }

      // Will be enabled in case of multi vendors
      // if(!$product->seller->isVerified()) {
      //   return $this->errorResponse('Seller must be verified', 409);
      // }

      if(!$product->isAvailable()) {
        return $this->errorResponse('Product is not available', 409);
      }

      return DB::transaction(function() use ($request, $product, $buyer) {
        $product->quantity -= $request->quantity;
        $product->save();

        $cart = $buyer->carts();

        $attr = ['product_id' => $product->id];
        
        if($cart->where($attr)->exists()) {
          $cart = $cart->where($attr)->first();

          $cart->amount += $request->amount;
          $cart->total = $cart->price * $cart->amount;
          $cart->save();
        } else {
          $cart = $product->addToCart([
            'buyer_id' => $buyer->id,
            'product_id' => $product->id,
            'price' => $product->price,
            'amount' => $request->amount,
            'total' => $product->price * $request->amount
          ]);
        }

        $carts = $buyer->carts;

        return $this->showAll($carts, 201);
      });
    }

    /**
      * Remove the specified resource from storage.
      *
      * @param  \App\Buyer  $buyer
      * @param  \App\Product  $product
      * @return \Illuminate\Http\Response
      */
    public function removeFromCart(Product $product, Buyer $buyer)
    {
      $attr = ['product_id' => $product->id];

      if(!$buyer->carts()->where($attr)->exists()) {
        return $this->errorResponse('No such product found in this cart', 404);
      }

      $cart = $buyer->carts()->where($attr)->first();
      $product->quantity += $cart->amount;
      $product->save();
      $cart->delete();

      return $this->showOne($cart);
    }
}