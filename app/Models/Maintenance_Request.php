<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Maintenance_Request extends Model
{
    use HasFactory;
    protected $dates = ['start_time', 'end_time'];
    protected $hidden = [
    ];

    protected $fillable = [
        'free_day',
        'number',
        'QR_code',
        'video',
        'notes',
        'Request_details',
        'latitude',
        'longitude',
        'location_type',
        'Request_state',
        'warranty_state',
        'consumable_parts',
        'repairs',
        'salary',
        'user_id',
        'team_id',
        'elec_id',
        'start_time',
        'end_time',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function team()
    {
        return $this->belongsTo(Maintenance_team::class ,'team_id');
    }
    public function elec()
    {
        return $this->belongsTo(Electrical_parts::class);
    }

    public function closestTeam()
    {
        $requestLatitude = $this->latitude;
        $requestLongitude = $this->longitude;
        $teams = Maintenance_team::where('location_type', 'team')->get();
        $closestTeam = null;
        $shortestDistance = PHP_INT_MAX;

        foreach ($teams as $team) {
            if ($team->status != 'Empty') {
                $teamLatitude = $team->latitude;
                $teamLongitude = $team->longitude;
                $distance = $this->haversine($requestLatitude, $requestLongitude, $teamLatitude, $teamLongitude);

                if ($distance < $shortestDistance) {
                    $closestTeam = $team;
                    $shortestDistance = $distance;
                }
            }
        }

        if (!$closestTeam && !$teams->isEmpty()) {
            $closestTeam = $teams->first();
        }

        return $closestTeam;
    }
    private function haversine($lat1, $lon1, $lat2, $lon2)
    {
        $radius = 6371;
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);
        $a = sin($dLat / 2) * sin($dLat / 2) +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($dLon / 2) * sin($dLon / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        $distance = $radius * $c;

        return $distance;
    }

    public function isConflicting($startTime, $endTime)
    {
        return self::where('team_id', $this->team_id)
            ->where(function ($query) use ($startTime, $endTime) {
                $query->whereBetween('start_time', [$startTime, $endTime])
                    ->orWhereBetween('end_time', [$startTime, $endTime])
                    ->orWhere(function ($query) use ($startTime, $endTime) {
                        $query->where('start_time', '<=', $startTime)
                            ->where('end_time', '>=', $endTime);
                    });
            })->exists();
    }

    public function schedule($startTime, $endTime)
    {
        if ($this->isConflicting($startTime, $endTime)) {
            throw new \Exception('The schedule conflicts with an existing request.');
        }

        $this->start_time = $startTime;
        $this->end_time = $endTime;
        $this->save();
    }
}
