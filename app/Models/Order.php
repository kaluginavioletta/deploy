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
        'type_product',
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
        return $this->hasMany(Status::class, 'id_status', 'id_status');
    }
    public function items()
    {
        return $this->hasMany(CartOrder::class, 'id_order', 'id_order');
    }

    public function users()
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }
}
