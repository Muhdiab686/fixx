<?php

namespace App\Http\Controllers\Worker;

use App\Http\Controllers\Controller;
use App\Models\LeaveRequest;
use App\Models\Worker;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use App\Models\Maintenance_Request;
use App\Models\Maintenance_team;


class WorkerController extends Controller
{
    public function Show_request(Request $request){
       
        $team_id = Worker::where('user_id', Auth()->user()->id)->first();
        if ($team_id) {
            $scheduledRequests = Maintenance_Request::where('team_id', $team_id->maintenance_team_id)
                ->whereNotNull('start_time')
                ->whereNotNull('end_time')
                ->get();

            $now = now();
            $hasCurrentRequest = $scheduledRequests->contains(function ($request) use ($now) {
                // Check if current time is within the start and end time
                return $now->between($request->start_time, $request->end_time);
            });

            if ($hasCurrentRequest) {
                Maintenance_team::where('id', $team_id->maintenance_team_id)->update(['state' => 'مشغول']);
            }

            return response()->json(['Maintenance Requests' => $scheduledRequests], 200);
        } else {
            return response()->json(['message' => 'Team not found'], 404);
        }
    }


    public function updateRequestByWorker(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer',
           // 'request_state' => 'string|max:255',
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

        $maintenanceRequest->request_state = "Pending";
        $maintenanceRequest->consumable_parts = $request->consumable_parts;
        $maintenanceRequest->repairs = $request->repairs;
        $maintenanceRequest->save();
        return response()->json(['message' => 'Maintenance request updated successfully by worker.', 'data' => $maintenanceRequest], 200);
    }

    public function requestLeave(Request $request)
    {
        $request->validate([
            'reason' => 'required|string|max:255',
        ]);

        $worker = Worker::where('user_id', Auth()->user()->id)->first();

        if (!$worker) {
            return response()->json(['error' => 'Worker not found.'], 404);
        }
        $endDate = Carbon::now()->addWeek();

        $leaveRequest = LeaveRequest::create([
            'worker_id' => $worker->id,
            'reason' => $request->reason,
            'status' => 'Pending',
            'end_date' => $endDate,
        ]);

        return response()->json(['message' => 'Leave request submitted successfully.', 'data' => $leaveRequest], 200);
    }

}
