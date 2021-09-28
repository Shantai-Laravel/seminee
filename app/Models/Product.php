<?php

namespace App\Models;

use App\Base as Model;

class Product extends Model
{
    protected $fillable = [
                'category_id',
                'promotion_id',
                'alias',
                'position',
                'succesion',
                'price',
                'dependable_price',
                'actual_price',
                'discount',
                'hit',
                'recomended',
                'stock',
                'code',
                'video',
                'discount_update',
                'com',
                'md',
            ];

    protected $appends = ['color'];

    public function getColorAttribute()
    {
        return null;
    }

    public function translations()
    {
        return $this->hasMany(ProductTranslation::class);
    }

    public function translation()
    {
        return $this->hasOne(ProductTranslation::class)->where('lang_id', self::$lang);
    }

    public function price()
    {
        return $this->hasOne(ProductPrice::class)->where('currency_id', self::$currency);
    }

    public function mainPrice()
    {
        return $this->hasOne(ProductPrice::class)->where('currency_id', self::$mainCurrency);
    }

    public function personalPrice()
    {
        return $this->hasOne(ProductPrice::class)->where('currency_id', self::$currency);
    }

    public function prices()
    {
        return $this->hasMany(ProductPrice::class)->orderBy('dependable', 'desc');
    }

    public function category()
    {
        return $this->hasOne(ProductCategory::class, 'id', 'category_id');
    }

    public function brands()
    {
        return $this->hasMany(ProductBrand::class, 'product_id', 'id');
    }

    public function collections()
    {
        return $this->hasMany(ProductCollection::class, 'product_id', 'id');
    }

    public function mainImage()
    {
        return $this->hasOne(ProductImage::class, 'product_id')->orderBy('main', 'desc');
    }

    public function setImage($setId)
    {
         return $this->hasOne(SetProductImage::class, 'product_id')->where('set_id', $setId);
    }

    public function setImages()
    {
        return  $this->hasMany(SetProductImage::class, 'product_id');
    }

    public function images()
    {
         return $this->hasMany(ProductImage::class, 'product_id')->orderBy('first', 'desc');
    }

    public function imagesBegin()
    {
         return $this->hasMany(ProductImage::class, 'product_id')->where('main', 1)->orWhere('first', 1)->orderBy('first', 'asc');
    }

    public function imagesLast()
    {
         return $this->hasMany(ProductImage::class, 'product_id')->where('main',  0)->where('first', 0);
    }

    public function inCart()
    {
        return $this->hasOne(Cart::class, 'product_id')->where('user_id', @$_COOKIE['user_id']);
    }

    public function inWishList()
    {
        $user_id = auth('persons')->id() ? auth('persons')->id() : @$_COOKIE['user_id'];
        return $this->hasOne(WishList::class, 'product_id')->where('user_id', $user_id);
    }

    public function similar()
    {
        return $this->hasMany(ProductSimilar::class);
    }

    public function similarProds()
    {
        return $this->hasMany(SimilarProdToProds::class, 'prod_main');
    }

    public function subproducts()
    {
        return $this->hasMany(SubProduct::class);
    }

    public function firstSubproduct()
    {
        return $this->hasOne(SubProduct::class)->where('stoc', '!=', 0);
    }

    public function subproductById($id)
    {
        return $this->hasOne(SubProduct::class)->where('id', $id);
    }

    public function property()
    {
        return $this->hasMany(SubProductProperty::class, 'product_category_id', 'category_id');
    }

    public function cart()
    {
        return $this->hasOne(Cart::class, 'product_id', 'id');
    }

    public function set()
    {
        return $this->hasOne(Set::class, 'id', 'set_id');
    }

    public function sets()
    {
        return $this->belongsToMany(Set::class, 'set_product');
    }

    public function propertyValues()
    {
        return $this->hasMany(ParameterValueProduct::class, 'product_id', 'id')->orderBy('id', 'desc');
    }

    public function materials()
    {
        return $this->hasMany(ProductMaterial::class, 'product_id', 'id');
    }
}
