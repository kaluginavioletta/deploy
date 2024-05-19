<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dessert extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $table = 'dessert';

    protected $primaryKey = 'id_dessert';

    protected $fillable = [
        'id_dessert',
        'name_dessert',
        'id_view_dessert',
        'compound_dessert',
        'price_dessert',
        'percent_discount_dessert',
        'discounted_price_dessert',
        'img_dessert'
    ];

    public function viewDessert()
    {
        return $this->belongsTo(ViewDessert::class, 'id_view_dessert', 'id_view_dessert');
    }
}
