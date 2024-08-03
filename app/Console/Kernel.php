<?php

namespace App\Console;

use App\Models\LeaveRequest;
use App\Models\Worker;
use Carbon\Carbon;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule){
        $schedule->call(function () {
            $today = Carbon::now()->toDateString();
            $leaveRequests = LeaveRequest::where('status', 'Approved')
                ->where('end_date', '<=', $today)
                ->get();

            foreach ($leaveRequests as $leaveRequest) {
                $worker = Worker::find($leaveRequest->worker_id);
                if ($worker) {
                    $worker->status = 'online';
                    $worker->maintenance_team_id = $leaveRequest->worker->maintenance_team_id; 
                    $worker->save();
                    $leaveRequest->status = 'Completed';
                    $leaveRequest->save();
                }
            }
        })->daily();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
