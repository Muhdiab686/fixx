<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Worker extends Model
{
    use HasFactory;

    protected $hidden = [
        'updated_at',
        'created_at',
    ];
    protected $table = '_worker';
    protected $fillable = [
        'user_id',
        'maintenance_team_id',
        'status'
    ];


    public function user(){
        return $this->belongsTo(User::class);
    }
    

    public function team(){
        return $this->belongsTo(Maintenance_team::class);
    }
}
