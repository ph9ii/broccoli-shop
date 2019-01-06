<?php

namespace App;

use App\Buyer;
use App\Product;
use App\Transformers\OrderTransformer;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use softDeletes;

    const PENDING_ORDER = 'pending';
    const COMPLETED_ORDER = 'completed';
    const INPROGRESS_ORDER = 'in-progress';

    protected static function boot()
    {
        parent::boot();
    }

    public $transformer = OrderTransformer::class;

    protected $table = 'orders';

    protected $dates = ['deleted_at'];

    protected $fillable = [
    	'buyer_id',
        'total',
        'address',
    	'status'
    ];

    public function buyer()
    {
        return $this->belongsTo(Buyer::class);
    }

    public function orderProducts()
    {
        return $this->belongsToMany(Product::class)
        ->withPivot('amount', 'total');
    }
}
