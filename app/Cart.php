<?php

namespace App;

use App\Buyer;
use App\Product;
use App\Transformers\CartTransformer;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
	public $transformer = CartTransformer::class;

	protected $table = 'carts';

    protected $fillable = [
        'buyer_id',
        'product_id',
        'amount',
        'price',
        'total'
    ];

    protected static function boot()
    {
        parent::boot();
    }

    public function product()
    {
        return $this->belongsTo(Product::class,'product_id');
    }

    public function buyer()
    {
        return $this->belongsTo(Buyer::class,'buyer_id');
    }
}
