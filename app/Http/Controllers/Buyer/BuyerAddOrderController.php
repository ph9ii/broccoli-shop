<?php

namespace App\Http\Controllers\Buyer;

use App\Buyer;
use App\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Transformers\OrderTransformer;
use App\Http\Controllers\ApiController;

class BuyerAddOrderController extends ApiController
{
    public function __construct()
    {
      parent::__construct();

      $this->middleware('transform.input:' . OrderTransformer::class)->only(['store']);
      $this->middleware('scope:purchase-product')->only(['store']);
      $this->middleware('can:purchase,buyer')->only('store');
    }

    /**
      * Store a newly created resource in storage.
      *
      * @param  \Illuminate\Http\Request  $request
      * @param  \App\Buyer  $buyer
      * @return \Illuminate\Support\Facades\DB
      * @return \Illuminate\Http\Response 
      */
    public function store(Request $request, Buyer $buyer)
    {
      $rules = [
        'address' => 'required|string|max:191',
      ];

      $this->validate($request, $rules);

      if(!$buyer->hasCarts()) {
        return $this->errorResponse('This buyer has no product(s) in his shopping cart', 409);
      }

      // Will rollback in-case of any errors
      return DB::transaction(function() use ($request, $buyer) {

        $cart_total = $buyer->carts()
          ->with('Products')
          ->sum('total');

        $transaction = $buyer->orders()->create([
          'address' => $request->address,
          'total' => $cart_total,
          'status' => Order::PENDING_ORDER
        ]);

        foreach ($buyer->carts as $cart) {
          $transaction->orderProducts()->attach($cart->product_id, [
            'amount'=> $cart->amount,
            'price'=> $cart->price,
            'total'=> $cart->total,
            'created_at' => $cart->created_at,
            'updated_at' => $cart->updated_at
          ]);
        }

        $buyer->carts()->delete();

        return $this->showOne($transaction, 201);
      });
    } 
}