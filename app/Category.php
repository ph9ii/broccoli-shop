<?php

namespace App;

use App\Product;
use App\Traits\Translatable;
use Illuminate\Database\Eloquent\Model;
use App\Transformers\CategoryTransformer;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
    use SoftDeletes, Translatable;

    public $transformer = CategoryTransformer::class;

    protected $dates = ['deleted_at'];
    
    protected $fillable = [
        'name',
        'description',
    ];

    protected $hidden = [
        'pivot',
    ];

    public function products()
    {
        return $this->belongsToMany(Product::class);
    }
}
