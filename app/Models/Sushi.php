<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class Sushi extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $table = 'sushi';

    protected $primaryKey = 'id';

    protected $fillable = [
        'name',
        'compound',
        'id_view_sushi',
        'price',
        'percent_discount',
        'discounted_price',
        'grams',
        'img'
    ];

    public function viewSushi()
    {
        return $this->belongsTo(ViewSushi::class, 'id_view_sushi', 'id_view_sushi');
    }

    public function cart_items()
    {
        return $this->morphMany(CartOrder::class, 'product');
    }

    protected static function booted()
    {
        static::created(function (Sushi $sushi) {
            $sushi->addProduct();
        });
        static::updated(function (Sushi $sushi) {
            $sushi->updateProduct();
        });
    }

    public function addProduct()
    {
        DB::table('products')->insert([
            'name' => $this->name,
            'compound' => $this->compound,
            'price' => $this->price,
            'type_product' => 'sushi',
            'percent_discount' => $this->percent_discount,
            'discounted_price' => $this->discounted_price,
            'grams' => $this->grams,
            'img' => $this->img,
        ]);
    }

    public function updateProduct()
    {
        DB::table('products')
            ->where('type_product', 'sushi')
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
