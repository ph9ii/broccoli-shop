<?php

namespace App\Http\Controllers\Seller;

use App\Order;
use App\Seller;
use Illuminate\Http\Request;
use App\Transformers\OrderTransformer;
use App\Http\Controllers\ApiController;
use Illuminate\Auth\Access\AuthorizationException;
use Symfony\Component\HttpKernel\Exception\HttpException;

class SellerOrderController extends ApiController
{
    public function __construct()
    {
      parent::__construct();
      $this->middleware('transform.input:' . OrderTransformer::class)->only(['update']);
      $this->middleware('scope:manage-orders')->except(['index']);
      $this->middleware('can:view,seller')->only('index');
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Seller $seller)
    {
      $orders = $seller->products()
        ->whereHas('orders')
        ->with('orders')
        ->get()
        ->pluck('orders')
        ->collapse()
        ->unique('id')
        ->values();

      return $this->showAll($orders);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Order  $order
     * @param  \App\Seller  $seller
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Seller $seller, Order $order)
    {
      $this->allowedAdminAction();

      $rules = [
        'status' => 'in:' . Order::PENDING_ORDER . ',' . Order::INPROGRESS_ORDER. ',' . Order::COMPLETED_ORDER
      ];

      $this->validate($request, $rules);

      // $this->checkSeller($seller);

      if($request->has('status')) {
        $order->status = $request->status;
      }

      if($order->isClean()) {
        return $this->errorResponse('You must specify different values', 422);
      }

      $order->save();

      return $this->showOne($order);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Seller  $seller
     * @return \Illuminate\Http\Response
     */
    public function destroy(Seller $seller, Order $order)
    {
      $this->allowedAdminAction();
      
      // $this->checkSeller($seller);

      // We are disabling this because we are using soft deletes on orders table
      // $order->orderProducts()->detach();

      $order->delete();

      return $this->showOne($order);
    }

    /**
     * Check if seller is admin
     * @param  $seller
     * @return Symfony\Component\HttpKernel\Exception\HttpException;
     */
    protected function checkSeller($seller)
    {
      if(!$seller->isAdmin()) {
        throw new HttpException(422, 'Only admins can change orders');
      }
    }
}