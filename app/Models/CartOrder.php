<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CartOrder extends Model
{
    use HasFactory;

    protected $table = 'cart_orders';

    protected $primaryKey = 'id';

    protected $fillable = [
        'id_cart',
        'id_user',
        'type_product',
        'id_product',
        'quantity',
        'total_price',
    ];

//    public $timestamps = false; // Если вам не нужны временные метки

//    protected $primaryKey = ['id_user', 'id_product']; // Составной ключ
    public $incrementing = true; // Отключаем автоинкремент

    public function order()
    {
        return $this->belongsTo(Order::class, 'id_user');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'id_product', 'id_product');
    }
}
