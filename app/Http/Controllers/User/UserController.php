<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Maintenance_team;
use App\Models\Worker;
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
            'free_day' => 'required|string|max:255',
            'number' => 'required|string|max:255',
            'QR_code' => 'required|image|max:255',
            'video' => 'required',
            'notes' => 'string',
            'request_details' => 'string',
            'electrical_part_id' => 'exists:electrical_parts,id'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $maintenanceRequest = new Maintenance_Request();
        $maintenanceRequest->free_day = $request->free_day;
        $maintenanceRequest->number = $request->number;
        if ($request->hasFile('QR_code')) {
            $imagePath = $request->file('QR_code')->store('public/qr_codes');
            $maintenanceRequest->QR_code = str_replace('public/', '', $imagePath);
        }

        if ($request->hasFile('video')) {
            $videoPath = $request->file('video')->store('public/videos');
            $maintenanceRequest->video = str_replace('public/', '', $videoPath);
        }
        $maintenanceRequest->notes = $request->notes;
        $maintenanceRequest->request_details = $request->request_details;
        $maintenanceRequest->user_id = Auth()->user()->id;
        $maintenanceRequest->electrical_part_id = $request->electrical_part_id;
        $maintenanceRequest->save();

        return response()->json(['message' => 'Maintenance request created successfully.', 'data' => $maintenanceRequest], 201);
    }



    public function userlocation(Request $request)
    {
        
        $validator =Validator::make($request->all(), [
            'point' => 'required',
          
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
//  
        $location = Location::create([
            'point'=> $request->point,
            'user_id' => Auth()->user()->id,
        ]);
        return response()->json(['message' => 'Location created successfully', 'location' => $location], 201);
    }
}
