<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Electrical_parts extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'size',
        'warning',
        'notes',
        'way_of_work'
    ];
    public function maintenance_Request(){
        return $this->hasMany(Maintenance_Request::class);
    }
}
