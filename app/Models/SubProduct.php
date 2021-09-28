<?php

namespace App\Models;

use App\Base as Model;

class SubProduct extends Model
{
    protected $table = 'subproducts';

    protected $fillable = ['product_id', 'parameter_id', 'value_id', 'code', 'combination', 'price', 'actual_price', 'dependable_price', 'discount', 'stoc', 'active'];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function cart()
    {
      return $this->hasOne(Cart::class, 'subproduct_id', 'id');
    }

    public function price()
    {
        return $this->hasOne(SubproductPrice::class, 'subproduct_id')->where('currency_id', self::$currency);
    }

    public function prices()
    {
        return $this->hasMany(SubproductPrice::class, 'subproduct_id')->orderBy('dependable', 'desc')->orderBy('currency_id', 'asc');
    }

    public function inWishList()
    {
        $user_id = auth('persons')->id() ? auth('persons')->id() : @$_COOKIE['user_id'];
        return $this->hasOne(WishList::class, 'subproduct_id')->where('user_id', $user_id);
    }

    public function parameter()
    {
      return $this->hasOne(Parameter::class, 'id', 'parameter_id');
    }

    public function parameterValue()
    {
      return $this->hasOne(ParameterValue::class, 'id', 'value_id');
    }
}
