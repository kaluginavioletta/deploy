<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Drink extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $primaryKey = 'id';

    protected $table = 'drinkables';

    protected $fillable = [
        'name',
        'compound',
        'id_view_drink',
        'price',
        'percent_discount',
        'discounted_price',
        'grams',
        'img',
    ];

    public function viewDrinkables()
    {
        return $this->belongsTo(ViewDrinkables::class, 'id_view_sushi', 'id_view_sushi');
    }

    public function cart_items()
    {
        return $this->morphMany(CartOrder::class, 'product');
    }

    protected static function booted()
    {
        static::created(function (Drink $drink) {
            $drink->addProduct();
        });
        static::updated(function (Drink $drink) {
            $drink->updateProduct();
        });
    }

    public function addProduct()
    {
        DB::table('products')->insert([
            'name' => $this->name,
            'compound' => $this->compound,
            'price' => $this->price,
            'type_product' => 'drink',
            'percent_discount' => $this->percent_discount,
            'discounted_price' => $this->discounted_price,
            'grams' => $this->grams,
            'img' => $this->img,
        ]);
    }
    public function updateProduct()
    {
        DB::table('products')
            ->where('type_product', 'drink')
            ->update([
                'name' => $this->name,
                'compound' => $this->compound,
                'price' => $this->price,
                'percent_discount' => $this->percent_discount,
                'discounted_price' => $this->discounted_price,
                'grams' => $this->grams,
                'img' => $this->img,
            ]);
    }
}
