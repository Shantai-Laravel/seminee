<?php

namespace App\Models;

use App\Base as Model;

class SimilarProdToProds extends Model
{
    protected $table = 'similar_prod_to_prod';

    protected $fillable = ['prod_main', 'prod_id'];
}
