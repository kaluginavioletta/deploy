<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Status extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $table = 'statuses';

    protected $primaryKey = 'id_status';

    protected $fillable = [
        'id_status',
        'name_status'
    ];

    public function name_status()
    {
        return $this->name_status;
    }
}
