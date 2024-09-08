<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasFactory;

    // Указываем таблицу, если имя таблицы не является множественным числом от имени модели
    protected $table = 'order_items';

    // Указываем, какие поля можно массово заполнять
    protected $fillable = [
        'id_order',
        'id_product',
        'quantity',
        'total_price',
    ];

    // Указываем, что модель не использует временные метки
    public $timestamps = false;

    // Определяем связь с моделью Order
    public function order()
    {
        return $this->belongsTo(Order::class, 'id_order', 'id_order');
    }

    // Определяем связь с моделью Product
    public function product()
    {
        return $this->belongsTo(Product::class, 'id_product', 'id_product');
    }
}
