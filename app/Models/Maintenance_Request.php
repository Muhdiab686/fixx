<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Maintenance_Request extends Model
{
    use HasFactory;
    protected $hidden = [
        'updated_at',
        'created_at',
    ];
    
    protected $fillable = [
        'free_day',
        'number',
        'QR_code',
        'video',
        'notes',
        'Request_details',
        'Request_state',
        'warranty_state',
        'consumable_parts',
        'repairs',
        'salary',
        'user_id',
        'electrical_part_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function electricalPart()
    {
        return $this->belongsTo(Electrical_parts::class);
    }

}
