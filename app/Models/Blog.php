<?php

namespace App\Models;

use App\Base as Model;

class Blog extends Model
{
    protected $fillable = [
                'category_id',
                'alias',
                'position',
                'active',
                'image',
            ];

    public function translations()
    {
        return $this->hasMany(BlogTranslation::class);
    }

    public function category()
    {
        return $this->hasOne(BlogCategory::class, 'id', 'category_id');
    }

    public function translation()
    {
        return $this->hasOne(BlogTranslation::class)->where('lang_id', self::$lang);
    }
}
