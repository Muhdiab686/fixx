<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Maintenance_team;
use App\Models\Worker;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
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
            'video' => 'string|max:255',
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
        $maintenanceRequest->QR_code = $request->QR_code;
        $maintenanceRequest->video = $request->video;
        $maintenanceRequest->notes = $request->notes;
        $maintenanceRequest->request_details = $request->request_details;
        $maintenanceRequest->user_id = Auth()->user()->id;
        $maintenanceRequest->electrical_part_id = $request->electrical_part_id;
        $maintenanceRequest->save();

        return response()->json(['message' => 'Maintenance request created successfully.', 'data' => $maintenanceRequest], 201);
    }

}
