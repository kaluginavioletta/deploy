<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\View\View;

class Sushi extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $table = 'sushi';

    protected $primaryKey = 'id_sushi';

    protected $fillable = [
        'id_sushi',
        'name_sushi',
        'compound_sushi',
        'id_view_sushi',
        'price_sushi',
        'percent_discount_sushi',
        'discounted_price_sushi',
        'grams',
        'img_sushi'
    ];

    public function viewSushi()
    {
        return $this->belongsTo(ViewSushi::class, 'id_view_sushi', 'id_view_sushi');
    }

    public function cart_items()
    {
        return $this->morphMany(CartOrder::class, 'product');
    }
}
