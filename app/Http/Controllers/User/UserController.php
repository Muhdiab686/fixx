<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Maintenance_team;
use App\Models\Worker;
use App\Models\Rating;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use App\Models\Location;
use App\Models\Maintenance_Request;
use Illuminate\Support\Facades\Hash;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class UserController extends Controller
{
    public function storeRequestByUser(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'free_day' => 'required|array',
            'number' => 'required|string|max:255',
            'QR_code' => 'required|image|max:255',
            'video' => 'required',
            'notes' => 'string',
            'request_details' => 'string',
            'latitude' => 'required',
            'longitude' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $maintenanceRequest = new Maintenance_Request();
        $maintenanceRequest->free_day = implode(',', $request->free_day);

        $maintenanceRequest->number = $request->number;
        
        if ($request->hasFile('QR_code')) {
            $photo = $request->QR_code;
            $newphoto = time() . $photo->getClientOriginalName();
            $photo->move(public_path('upload'), $newphoto);
            $path = "public/upload/$newphoto";
            $maintenanceRequest->QR_code = $path;
        }

        if ($request->hasFile('video')) {
            $photo = $request->video;
            $newphoto = time() . $photo->getClientOriginalName();
            $photo->move(public_path('upload'), $newphoto);
            $path = "public/upload/$newphoto";
            $maintenanceRequest->video =$path;
        }

        $maintenanceRequest->notes = $request->notes;
        $maintenanceRequest->request_details = $request->request_details;
        $maintenanceRequest->latitude = $request->latitude;
        $maintenanceRequest->longitude = $request->longitude;
        $maintenanceRequest->user_id = Auth()->user()->id;
        $closestTeam = $maintenanceRequest->closestTeam();
        $maintenanceRequest->team()->associate($closestTeam);
        $maintenanceRequest->elec_id = $request->elec_id;
        $maintenanceRequest->save();

       


        $team = Maintenance_team::where('id',$maintenanceRequest->team_id)->first();
        $team->state = 'Busy';
        $team->update();
        
        return response()->json(['message' => 'Maintenance request created successfully.', 'data' => $maintenanceRequest], 201);
    }


    public function rate_maintenance_team(Request $request)
    {


        $rules = [
            'star' => 'required|integer|between:1,5',
            'maintenance_team_id' => 'required',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        if (!Maintenance_team::find($request->maintenance_team_id)) {
            return response()->json(['message' => 'Invild ID'], 422);
        }


        $user = Auth()->user()->id;
        $israting = Rating::where('maintenance_team_id', $request->maintenance_team_id)->where('user_id', $user)->get();
        if ($israting) {
            return response()->json(['message' => 'you have rate Maintenance_team before .'], 201);
        }


        $rating = Rating::create([
            'star' => $request->star,
            'maintenance_team_id' => $request->maintenance_team_id,
            'user_id' => $user
        ]);
        return response()->json(['message' => 'Maintenance_team have rate successfully.', 'data' => $rating], 201);
    }

    public function show_rating(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'maintenance_team_id' => 'required',
        ]);

        if (!Maintenance_team::find($request->maintenance_team_id)) {
            return response()->json(['message' => 'Invild ID'], 422);
        }

        $rating = Rating::where('maintenance_team_id', $request->maintenance_team_id)->get();

        $starsum = 0;
        $ratingcount = 0;
        $Avg = 0;
        foreach ($rating as $rate) {

            $starsum += $rate->star;
            $ratingcount += 1;
        }
        $Avg = $starsum / $ratingcount;
        if ($Avg == 0) {
            return response()->json(['message' => 'no one has rate the maintenance team.']);
        }

        return response()->json(['message' => 'avg of rating', 'data' => $rating], 201);


    }

    public function destroyrate(Request $request)
    {

        $user = User::find(auth()->id());
        $validator = Validator::make($request->all(), [
            'maintenance_team_id' => 'required',
        ]);


        $israting = Rating::where('maintenance_team_id', $request->maintenance_team_id)->where('user_id', $user)->first();
        if ($israting == null) {
            return response()->json(['message' => 'you have not rate Maintenance_team before .']);
        }

        $rating = Rating::where('maintenance_team_id', $request->maintenance_team_id)->where('user_id', $user)->get();
        $rating->delete();

        return response()->json(['message' => 'the rating is removed successfully.'], 201);
    }

}
