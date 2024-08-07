<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $table = 'products';

    protected $primaryKey = 'id_product';

    protected $fillable = [
        'name',
        'compound',
        'price',
        'type_product',
        'percent_discount',
        'discounted_price',
        'grams',
        'img',
    ];
}
