<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rating extends Model
{
    use HasFactory;
    protected $fillable = [
        'star',
        'nots',
        'maintenance_team_id',
        'user_id'
    ];

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function maintenance_team(){
        return $this->belongsTo(Maintenance_team::class);
    }
}
