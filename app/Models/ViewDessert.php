<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ViewDessert extends Model
{
    use HasFactory;

    protected $table = 'view_dessert';

    protected $primaryKey = 'id_view_dessert';

    protected $fillable = [
        'name_view_dessert',
    ];
}
