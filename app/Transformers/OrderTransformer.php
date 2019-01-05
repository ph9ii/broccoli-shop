<?php

namespace App\Transformers;

use App\Order;
use League\Fractal\TransformerAbstract;

class OrderTransformer extends TransformerAbstract
{
    /**
     * A Fractal transformer.
     *
     * @return array
     */
    public function transform(Order $order)
    {
        return [
            'identifier'   => (int) $order->id,
            'address'      => (string) $order->address,
            'buyerID'      => (int) $order->buyer_id,
            'orderStatus'  => (string) $order->status,
            'sumTotal'     => (string) number_format($order->total, 2),
            'creationDate' => (string) $order->created_at,
            'lastChange'   => (string) $order->updated_at,

            'links' => [
                [
                    'rel' => 'self',
                    'href' => route('orders.show', $order->id),
                ],
                [
                    'rel' => 'order.categories',
                    'href' => route('orders.categories.index', $order->id),
                ],
                [
                    'rel' => 'order.seller',
                    'href' => route('orders.sellers.index', $order->id),
                ],
                [
                    'rel' => 'buyer',
                    'href' => route('buyers.show', $order->buyer_id),
                ],
                [
                    'rel' => 'product',
                    'href' => route('orders.products.index', $order->id),
                ],
            ]
        ];
    }

    public static function originalAttribute($index)
    {
        $attributes = [
            'identifier'   => 'id',
            'address'      => 'address',
            'sumTotal'     => 'total',
            'buyerID'      => 'buyer_id',
            'orderStatus'  => 'status',
            'creationDate' => 'created_at',
            'lastChange'   => 'updated_at',
        ];

        return isset($attributes[$index]) ? $attributes[$index] : null;
    }

    public static function transformedAttribute($index)
    {
        $attributes = [
            'id'         => 'identifier',
            'address'    => 'address',
            'total'      => 'sumTotal',
            'buyer_id'   => 'buyerID',
            'status'     => 'orderStatus',
            'created_at' => 'creationDate',
            'updated_at' => 'lastChange',
        ];

        return isset($attributes[$index]) ? $attributes[$index] : null;
    }
}
