<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $table = 'orders';

    protected $primaryKey = 'id_order';

    protected $fillable = [
        'id_user',
        'id_address',
        'id_status',
        'total_price'
    ];

    public function address()
    {
        return $this->belongsTo(Address::class, 'id_address', 'id_address');
    }

    public function status()
    {
        return $this->belongsTo(Status::class, 'id_status', 'id_status');
    }

    public function hasProductType($productType)
    {
        return $this->items()->where('product_type', $productType)->exists();
    }

    public function cart_orders()
    {
        return $this->hasMany(CartOrder::class, 'id_order', 'id_order');
    }

    public function calculateTotalPrice()
    {
        $totalPrice = 0;
        foreach ($this->items as $item) {
            $totalPrice += $item->quantity * $item->price;
        }

        $this->total_price = $totalPrice;
        $this->save();
    }

    public function items()
    {
        return $this->hasMany(CartOrder::class, 'id_order');
    }
}
