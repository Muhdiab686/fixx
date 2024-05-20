<?php

namespace App\Models;

use App\Models\Electrical_parts;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QRcode extends Model
{
    use HasFactory;


    protected $hidden = [
        'updated_at',
        'created_at',
    ];

    protected $fillable = [
        'QR_base64',
        'electrical_part_id',
    ];
    public function part()
    {
        return $this->belongsTo(Electrical_parts::class,'electrical_part_id');
    }
}
