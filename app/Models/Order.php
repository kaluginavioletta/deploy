<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'id_order',
        'id_drink',
        'id_sushi',
        'id_dessert',
        'count_order',
        'id_address',
        'id_status',
        'price_order'
    ];

    public function drink()
    {
        return $this->belongsTo(Drink::class, 'id_drink', 'id_drink');
    }

    public function sushi()
    {
        return $this->belongsTo(Sushi::class, 'id_sushi', 'id_sushi');
    }

    public function dessert()
    {
        return $this->belongsTo(Dessert::class, 'id_dessert', 'id_dessert');
    }

    public function address()
    {
        return $this->belongsTo(Address::class, 'id_address', 'id_address');
    }

    public function status()
    {
        return $this->belongsTo(Status::class, 'id_status', 'id_status');
    }
}
