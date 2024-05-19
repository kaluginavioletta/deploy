<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Drink extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $primaryKey = 'id_drink';

    protected $table = 'drinkables';

    protected $fillable = [
        'id_drink',
        'name_drink',
        'id_view_drink',
        'compound_drink',
        'price_drink',
        'percent_discount_drink',
        'discounted_price_drink',
        'img_drink'
    ];

    public function viewDrinkables()
    {
        return $this->belongsTo(ViewDrinkables::class, 'id_view_sushi', 'id_view_sushi');
    }

}
