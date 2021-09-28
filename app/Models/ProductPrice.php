<?php

namespace App\Models;

use App\Base as Model;

class ProductPrice extends Model
{
    protected $table = 'product_prices';

    protected $fillable = ['product_id', 'currency_id', 'old_price', 'price', 'dependable'];

    public function product()
    {
        return $this->hasOne(Product::class);
    }

    public function currency()
    {
        return $this->hasOne(Currency::class, 'id', 'currency_id');
    }

}
