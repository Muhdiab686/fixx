<?php

namespace App\Http\Controllers\Worker;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use App\Models\Maintenance_Request;

class WorkerController extends Controller
{
    public function updateRequestByWorker(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer',
            'request_state' => 'string|max:255',
            'consumable_parts' => 'string|max:255',
            'repairs' => 'string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $maintenanceRequest = Maintenance_Request::find($request->id);

        if (!$maintenanceRequest) {
            return response()->json(['error' => 'Maintenance request not found.'], 404);
        }

        $maintenanceRequest->request_state = $request->request_state;
        $maintenanceRequest->consumable_parts = $request->consumable_parts;
        $maintenanceRequest->repairs = $request->repairs;
        $maintenanceRequest->save();

        return response()->json(['message' => 'Maintenance request updated successfully by worker.', 'data' => $maintenanceRequest], 200);
    }


}
