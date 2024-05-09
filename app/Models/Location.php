<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    use HasFactory;

    protected $fillable = [
        'city',
        'region',
        'coordinates',
        'user_id'
    ];
    public function user(){
        return $this->hasOne(User::class);
    }
}
