<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    use HasFactory;

    protected $hidden = [
        'updated_at',
        'created_at',
    ];
    protected $fillable = [
        'longitude',
        'latitude',
        'user_id'
    ];
    public function user(){
        return $this->hasOne(User::class);
    }
}
