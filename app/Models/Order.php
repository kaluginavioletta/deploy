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

    public function item()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function hasProductType($productType)
    {
        return $this->item()->where('product_type', $productType)->exists();
    }
}
