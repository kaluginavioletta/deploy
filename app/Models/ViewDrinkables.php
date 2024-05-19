<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ViewDrinkables extends Model
{
    use HasFactory;

    protected $table = 'view_drinkables';

    protected $primaryKey = 'id_view_drink';

    protected $fillable = [
        'name_view_drink',
    ];
}
