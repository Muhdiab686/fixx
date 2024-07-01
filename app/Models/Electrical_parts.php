<?php

namespace App\Models;

use App\Models\QRcode;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Electrical_parts extends Model
{
    use HasFactory;
   protected $hidden = [
         'updated_at',
        'created_at',
    ];
    
    protected $fillable = [
        'name',
        'photo',
        'size',
        'warning',
        'notes',
        'way_of_work',
        'warranty_state',
        'warranty_date',
    ];
    public function maintenance_Request(){
        return $this->hasMany(Maintenance_Request::class);
    }

    public function qrcode()
    {
        return $this->hasOne(\App\Models\QRcode::class);
    }

}
