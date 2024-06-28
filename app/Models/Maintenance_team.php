<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Maintenance_team extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'state',
        'latitude',
        'longitude',
        'location_type'
    ];
    protected $hidden = [
        'updated_at',
        'created_at',
    ];
    public function worker()
    {
        return $this->hasMany(Worker::class);
    }
    public function request()
    {
        return $this->hasMany(Maintenance_Request::class);
    }

    public function rating(){
        return $this->hasMany(Rating::class);
    }
}

