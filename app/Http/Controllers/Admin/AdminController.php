<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LeaveRequest;
use App\Models\Maintenance_team;
use App\Models\Worker;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use App\Models\Electrical_parts;
use App\Models\Maintenance_Request;
use Illuminate\Support\Facades\Hash;
use Psy\Command\WhereamiCommand;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use GuzzleHttp\Client;
use App\Models\Location;
use GeoDistance\GeoDistance;
use Carbon\Carbon;

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
        if ($request->hasFile('photo')) {
            $photo = $request->photo;
            $newphoto = time() . $photo->getClientOriginalName();
            $photo->move(public_path('upload'), $newphoto);
            $path = "public/upload/$newphoto";
        }
        $item = Electrical_parts::create([
            'name' => $request->input('name'),
            'photo' =>$path,
            'size' => $request->input('size'),
            'warning' => $request->input('warning'),
            'notes' => $request->input('notes'),
            'way_of_work' => $request->input('way_of_work'),
            'warranty_state'=> $request->input('warranty_state'),
            'warranty_date'=> $request->input('warranty_date'),
        ]);

        $qrCode = QrCode::size(200)->generate($item->id);
        $QR =  \App\Models\QRcode::create([
            'QR_base64'=>base64_encode($qrCode),
            'electrical_part_id'=> $item->id,
        ]);
        return response()->json([
            'message' => 'Done',
            'qr_code' => $QR
        ], 201);

    }

    public function ShowElectrical(Request $request){
        $part = Electrical_parts::get();
        return response()->json($part);
    }

    public function show_qr(Request $request){

        $qr =  \App\Models\QRcode::where("id" ,$request->input('QRcode'))->with('part')->first();
         $q = $qr->electrical_part_id;
        $request = Maintenance_Request::where('elec_id',$q)->with('elec')->get();
            return response()->json([
                'message' => 'Done',
                'qr_code' => $request
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
                'State_team' =>$team->state,
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
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer',
            'salary' => 'string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $maintenanceRequest = Maintenance_Request::find($request->id);

        if (!$maintenanceRequest) {
            return response()->json(['error' => 'Maintenance request not found.'], 404);
        }
        if($maintenanceRequest->QR_code){
            $maintenanceRequest->warranty_state = 'مكفول';
        }else{
            $maintenanceRequest->warranty_state = 'غير مكفول';
        }
       
        $maintenanceRequest->salary = $request->salary;
        $maintenanceRequest->save();

        return response()->json(['message' => 'Maintenance request updated successfully by admin.', 'data' => $maintenanceRequest], 200);
    }
    public function Pending_report(Request $request){
        $report = Maintenance_Request::with('user','team')->Where('Request_state', 'Pending')->get();
        return response()->json($report);
    }
    public function report(Request $request)
    {
        $report = Maintenance_Request::with('user', 'team')->get();
        return response()->json($report);
    }

    public function finish_report(Request $request)
    {
        $report = Maintenance_Request::with('user', 'team')->Where('Request_state','Complete')->get();
        return response()->json($report);
    }

    public function Schedling(Request $request ){ {
            $startTime = Carbon::parse($request->input('start_time'));
            $endTime = Carbon::parse($request->input('end_time'));
            
            try {
                $maintenanceRequest = Maintenance_Request::findOrFail($request->requestId);

                if ($maintenanceRequest->isConflicting($startTime, $endTime)) {
                    throw new \Exception('The schedule conflicts with an existing request.');
                }
               
                $maintenanceRequest->schedule($startTime, $endTime);

                return response()->json([
                    'success' => true,
                    'message' => 'Maintenance request scheduled successfully.'
                ]);
            }
            
            catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 400);
            }
        }
    }

    public function GenerateStatistics(Request $request){
        
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $totalRequests = Maintenance_Request::whereBetween('created_at', [$startDate, $endDate])->count();
        $pendingRequests = Maintenance_Request::whereBetween('created_at', [$startDate, $endDate])->where('request_state', 'Pending')->count();
        $completedRequests = Maintenance_Request::whereBetween('created_at', [$startDate, $endDate])->where('request_state', 'Complete')->count();

        return response()->json([
            'total_requests' => $totalRequests,
            'pending_requests' => $pendingRequests,
            'completed_requests' => $completedRequests,
        ]);
    }

    public function GenerateRatio(Request $request)
    {
        $request->validate([
            'start_month' => 'required|date_format:Y-m',
            'end_month' => 'required|date_format:Y-m|after_or_equal:start_month',
        ]);

        $startMonth = new Carbon($request->input('start_month'));
        $endMonth = new Carbon($request->input('end_month'));

        $startYear = $startMonth->year;
        $endYear = $endMonth->year;

        $startMonthNumber = $startMonth->month;
        $endMonthNumber = $endMonth->month;

        $requestsStartMonth = Maintenance_Request::whereYear('created_at', $startYear)
            ->whereMonth('created_at', $startMonthNumber)
            ->count();

        $requestsEndMonth = Maintenance_Request::whereYear('created_at', $endYear)
            ->whereMonth('created_at', $endMonthNumber)
            ->count();

        // Calculate ratio
        if ($requestsStartMonth > 0) {
            $ratio = (($requestsEndMonth - $requestsStartMonth) / $requestsStartMonth) * 100;
        } else {
            $ratio = null;
        }

        return response()->json([
            'start_month' => $startMonth->format('Y-m'),
            'end_month' => $endMonth->format('Y-m'),
            'requests_start_month' => $requestsStartMonth,
            'requests_end_month' => $requestsEndMonth,
            'ratio' => $ratio."%",
        ]);
    }


    public function Showschedling(Request $request){
        $schedule = Maintenance_Request::whereNotNull('start_time')
            ->whereNotNull('end_time')
            ->get();
            return response()->json([
                'success' => true,
                'message' => $schedule
            ], 400);
    }
  public function Shownotschedling(Request $request){
    $shudle = Maintenance_Request::whereNull('start_time')
        ->orWhereNull('end_time')
        ->get();
        return response()->json([
            'success' => true,
            'message' => $shudle
        ], 400);
    }

    public function handleLeaveRequest(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:Approved,Rejected',
        ]);

        $leaveRequest = LeaveRequest::find($id);

        if (!$leaveRequest) {
            return response()->json(['error' => 'Leave request not found.'], 404);
        }

        DB::transaction(function () use ($leaveRequest, $request) {
            $leaveRequest->status = $request->status;
            $leaveRequest->save();

            if ($request->status == 'Approved') {
                $worker = Worker::find($leaveRequest->worker_id);
                $worker->status = 'offline';
                $worker->maintenance_team_id = null; 
                $worker->save();
            }
        });
        return response()->json(['message' => 'Leave request updated successfully.', 'data' => $leaveRequest], 200);
    }

}