<?php

namespace App\Models;

use App\Base as Model;
use App\Models\Product;

class ProductCategory extends Model
{
    protected $table = 'product_categories';

    protected $fillable = [
                        'parent_id',
                        'alias',
                        'level',
                        'position',
                        'succesion',
                        'on_home',
                        'active',
                        'icon',
                        'banner_desktop',
                        'banner_mobile',
                        'product_type'
                    ];

    public function translations()
    {
        return $this->hasMany(ProductCategoryTranslation::class);
    }

    public function translation()
    {
        return $this->hasOne(ProductCategoryTranslation::class)->where('lang_id', self::$lang);
    }

    public function translationByLang($lang)
    {
        return $this->hasOne(ProductCategoryTranslation::class)->where('lang_id', $lang);
    }

    public function properties() {
        return $this->hasMany(SubProductParameter::class, 'category_id', 'id');
    }

    public function property() {
        return $this->hasOne(SubProductParameter::class, 'category_id', 'id');
    }

    public function subproductParameter()
    {
        return $this->hasOne(SubProductParameter::class, 'category_id', 'id');
    }

    public function products() {
        return $this->hasMany(Product::class, 'category_id', 'id')->orderBy('position', 'asc');
    }

    public function parent()
    {
        return $this->belongsTo(ProductCategory::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(ProductCategory::class, 'parent_id')->orderBy('position', 'asc') ->orderBy('created_at', 'desc');
    }

    public function params()
    {
        return $this->hasMany(ParameterCategory::class, 'category_id')->orderBy('position', 'asc');
    }

    public function paremeterCategory($parameterId)
    {
        return $this->hasOne(ParameterCategory::class, 'category_id')->where('parameter_id', $parameterId);
    }

    public function parametersGroups()
    {
        return $this->hasMany(ParameterGroupCategory::class, 'category_id');
    }

}
