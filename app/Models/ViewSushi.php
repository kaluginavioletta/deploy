<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ViewSushi extends Model
{
    use HasFactory;

    protected $table = 'view_sushi';

    protected $primaryKey = 'id_view_sushi';

    protected $fillable = [
        'name_view_sushi',
    ];
}
