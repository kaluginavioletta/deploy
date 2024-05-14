<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Favorite extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'id_user',
        'id_sushi',
        'id_drink',
        'id_dessert'
    ];

    public function users()
    {
        return $this->belongsTo(User::class, 'id_user', 'id');
    }

    public function sushi()
    {
        return $this->belongsTo(Sushi::class, 'id_sushi', 'id_sushi');
    }

    public function drink()
    {
        return $this->belongsTo(Drink::class, 'id_drink', 'id_drink');
    }

    public function dessert()
    {
        return $this->belongsTo(Dessert::class, 'id_dessert', 'id_dessert');
    }
}
