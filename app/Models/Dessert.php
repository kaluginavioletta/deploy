<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dessert extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'id_dessert',
        'name_dessert',
        'desc_dessert',
        'price_dessert',
        'discounted_price_dessert',
        'img_dessert'
    ];
}
