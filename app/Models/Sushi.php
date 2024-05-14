<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sushi extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $table = 'sushi';

    protected $fillable = [
        'id_sushi',
        'name_sushi',
        'desc_sushi',
        'price_sushi',
        'discounted_price_sushi',
        'grams',
        'img_sushi'
    ];
}
