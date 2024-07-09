<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Dessert extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $table = 'dessert';

    protected $primaryKey = 'id';

    protected $fillable = [
        'name',
        'compound',
        'id_view_dessert',
        'price',
        'percent_discount',
        'discounted_price',
        'grams',
        'img',
    ];

    public function viewDessert()
    {
        return $this->belongsTo(ViewDessert::class, 'id_view_dessert', 'id_view_dessert');
    }

    public function cart_items()
    {
        return $this->morphMany(CartOrder::class, 'product');
    }

    protected static function booted()
    {
        static::created(function (Dessert $dessert) {
            $dessert->addProduct();
        });
        static::updated(function (Dessert $dessert) {
            $dessert->updateProduct();
        });
    }

    public function addProduct()
    {
        DB::table('products')->insert([
            'name' => $this->name,
            'compound' => $this->compound,
            'price' => $this->price,
            'type_product' => 'dessert',
            'percent_discount' => $this->percent_discount,
            'discounted_price' => $this->discounted_price,
            'grams' => $this->grams,
            'img' => $this->img,
        ]);
    }

    public function updateProduct()
    {
        DB::table('products')
            ->where('type_product', 'dessert')
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
