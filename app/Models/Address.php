<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $table = 'addresses';

    protected $primaryKey = 'id_address';

    protected $fillable = [
        'id_address',
        'address_city',
        'address_street',
        'address_home',
        'address_entrance',
        'address_floor',
        'address_apartment'
    ];
}
