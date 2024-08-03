<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeaveRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'worker_id',
        'status',
        'reason',
        'end_date'
    ];

    public function worker()
    {
        return $this->belongsTo(Worker::class);
    }
}
