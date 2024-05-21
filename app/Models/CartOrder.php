<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CartOrder extends Model
{
    use HasFactory;

    protected $table = 'cart_orders';

    protected $primaryKey = 'id_cart';

    protected $fillable = [
        'id_user',
        'id_order',
        'type_product',
        'id_product',
        'quantity',
        'price',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function removeFromCart()
    {
        $this->delete();
    }

    public function items()
    {
        return $this->hasMany(Order::class, 'id_order', 'id_order');
    }

    public function increaseQuantityAndUpdatePrice($quantity)
    {
        $this->quantity += $quantity;
        $this->price = $this->quantity * $this->price;
        $this->save();
    }
}
