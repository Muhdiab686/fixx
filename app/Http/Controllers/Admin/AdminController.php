<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Maintenance_team;
use App\Models\Worker;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use App\Models\Electrical_parts;
use App\Models\Maintenance_Request;
use Illuminate\Support\Facades\Hash;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use GuzzleHttp\Client;
use App\Models\Location;


class AdminController extends Controller
{
    public function AddWorker(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'team_id' => 'required|integer|exists:maintenance_teams,id',
        ]);

        if ($validator->fails()) {

            return response()->json($validator->errors(), 422);
        }
        $currentWorkersCount = Worker::where('maintenance_team_id', $request->team_id)->count();
        if ($currentWorkersCount >= 4) {
            return response()->json(['error' => 'The team already has 4 workers.'], 400);
        }
        $email = $request->name . random_int(1000, 9999) . "@gmail.com";
        if (User::where('email', $request->email)->exists()) {
            $email = $request->name . random_int(1000, 9999) . "@gmail.com";
            return;
        }
        $password = $request->name . random_int(1000, 9999);
        $user = User::create([
            'name' => $request->name,
            'email' => $email,
            'password' => Hash::make($password),
            'role' => 'worker'
        ]);
        $worker = new Worker();
        $worker->user_id = $user->id;
        $worker->maintenance_team_id = $request->team_id;
        $worker->save();
        return response()->json(['email' => $email,'password'=> $password, 'worker' => $worker], 200);
    }
    public function DeleteWorker(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'worker_id' => 'required|integer|exists:_worker,id',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $worker = Worker::find($request->worker_id);
        if (!$worker) {
            return response()->json(['error' => 'Worker not found.'], 404);
        }
        $worker->delete();
        $user = User::find($worker->user_id);
        if ($user) {
            $user->delete();
        }
        return response()->json(['message' => 'Worker and associated user deleted successfully.'], 200);
    }

    public function AddElectrical(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'size' => 'required|integer',
            'warning' => 'required|string',
            'notes' => 'nullable|string',
            'way_of_work' => 'nullable|string',
            'warranty_state'=> 'required|string',
            'warranty_date'=> 'required',
        ]);
        if ($validator->fails()) {

            return response()->json($validator->errors(), 422);
        }
        $item = Electrical_parts::create([
            'name' => $request->input('name'),
            'size' => $request->input('size'),
            'warning' => $request->input('warning'),
            'notes' => $request->input('notes'),
            'way_of_work' => $request->input('way_of_work'),
            'warranty_state'=> $request->input('warranty_state'),
            'warranty_date'=> $request->input('warranty_date'),
        ]);

        $qrCode = QrCode::size(200)->generate($item);
        $QR =  \App\Models\QRcode::create([
            'QR_base64'=>base64_encode($qrCode),
            'electrical_part_id'=> $item->id,
        ]);
        return response()->json([
            'message' => 'Done',
            'qr_code' => $QR
        ], 201);

    }


        $qr =  \App\Models\QRcode::where("QR_base64" ,$request->input('QRcode'))->with('part')->get();
            return response()->json([
                'message' => 'Done',
                'qr_code' => $qr
            ], 201) ;
    }

    public function Show_Team(Request $request)
    {
        $teams = Maintenance_team::all();
        $teamsInfo = [];
        foreach ($teams as $team) {
            $currentWorkersCount = Worker::where('maintenance_team_id', $team->id)->count();
            $teamsInfo[] = [
                'id'=> $team->id,
                'team_name' => $team->name,
                'current_workers_count' => $currentWorkersCount
            ];
        }
        return response()->json($teamsInfo, 200);
    }
    public function Show_Worker(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'team_id' => 'required|integer|exists:maintenance_teams,id',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $workers = Worker::where('maintenance_team_id', $request->team_id)->with('user')->get();
        return response()->json($workers, 200);
    }

    public function updateRequestByAdmin(Request $request)
    {

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $maintenanceRequest = Maintenance_Request::find($request->id);

        if (!$maintenanceRequest) {
            return response()->json(['error' => 'Maintenance request not found.'], 404);
        }
        $maintenanceRequest->warranty_state = $request->warranty_state;
        $maintenanceRequest->salary = $request->salary;
        $maintenanceRequest->save();

        return response()->json(['message' => 'Maintenance request updated successfully by admin.', 'data' => $maintenanceRequest], 200);
    }
}