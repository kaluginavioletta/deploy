<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Drink extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'id_drink',
        'name_drink',
        'desc_drink',
        'price_drink',
        'discounted_price_drink',
        'img_drink'
    ];

    public function order()
    {
        return $this->hasMany(Order::class, 'id_drink', 'id_drink');
    }

}
