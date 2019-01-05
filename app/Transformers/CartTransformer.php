<?php

namespace App\Transformers;

use App\Cart;
use League\Fractal\TransformerAbstract;

class CartTransformer extends TransformerAbstract
{
    /**
     * A Fractal transformer.
     *
     * @return array
     */
    public function transform(Cart $cart)
    {
        return [
            'identifier'   => (int) $cart->id,
            'quantity'     => (int) $cart->amount,
            'buyerID'      => (int) $cart->buyer_id,
            'productID'    => (int) $cart->product_id,
            'price'        => (string) number_format($cart->price),
            'sumTotal'     => (string) number_format($cart->total, 2),
            'creationDate' => (string) $cart->created_at,
            'lastChange'   => (string) $cart->updated_at,

            'links' => [
                [
                    'rel' => 'self',
                    'href' => route('carts.show', $cart->id),
                ],
                [
                    'rel' => 'cart.categories',
                    'href' => route('carts.categories.index', $cart->id),
                ],
                [
                    'rel' => 'cart.seller',
                    'href' => route('carts.sellers.index', $cart->id),
                ],
                [
                    'rel' => 'buyer',
                    'href' => route('buyers.show', $cart->buyer_id),
                ],
                [
                    'rel' => 'product',
                    'href' => route('products.show', $cart->product_id),
                ],
            ]
        ];
    }

    public static function originalAttribute($index)
    {
        $attributes = [
            'identifier'   => 'id',
            'quantity'     => 'amount',
            'price'        => 'price',
            'sumTotal'     => 'total',
            'buyerID'      => 'buyer_id',
            'productID'    => 'product_id',
            'creationDate' => 'created_at',
            'lastChange'   => 'updated_at',
        ];

        return isset($attributes[$index]) ? $attributes[$index] : null;
    }

    public static function transformedAttribute($index)
    {
        $attributes = [
            'id'         => 'identifier',
            'amount'     => 'quantity',
            'price'      => 'price',
            'total'      => 'sumTotal',
            'buyer_id'   => 'buyerID',
            'product_id' => 'productID',
            'created_at' => 'creationDate',
            'updated_at' => 'lastChange',
        ];

        return isset($attributes[$index]) ? $attributes[$index] : null;
    }
}
