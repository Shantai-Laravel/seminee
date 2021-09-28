<?php
namespace App\Models;

use App\Base as Model;

class ParameterValue extends Model
{
    protected $table = 'parameter_values';

    protected $fillable = ['parameter_id', 'image'];

    public function translations()
    {
        return $this->hasMany(ParameterValueTranslation::class);
    }

    public function translation()
    {
        return $this->hasOne(ParameterValueTranslation::class , 'parameter_value_id')->where('lang_id', self::$lang);
    }

    public function transData()
    {
        return $this->hasOne(ParameterValueTranslation::class , 'parameter_value_id');
    }
}
