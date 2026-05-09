<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\SupplyRequest;
use App\Models\Notification;
use App\Services\AuditService;
use App\Events\NotificationSent;
use Carbon\Carbon;

class AutoDisapproveRequests extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'requests:auto-disapprove';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automatically disapprove pending requests older than 5 days';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $cutoffDate = Carbon::now()->subDays(5);

        $pendingRequests = SupplyRequest::where('status', 'pending')
            ->where('created_at', '<=', $cutoffDate)
            ->get();

        if ($pendingRequests->isEmpty()) {
            $this->info('No pending requests found older than 5 days.');
            return;
        }

        $count = 0;
        foreach ($pendingRequests as $request) {
            $oldValues = $request->toArray();
            
            $request->status = 'disapproved';
            $request->save();

            // Log the action
            AuditService::log(
                'UPDATE', 
                $request, 
                "Automatically disapproved request due to inactivity (over 5 days)", 
                $oldValues, 
                $request->fresh()->toArray()
            );

            // Send notification
            $notif = Notification::create([
                'user_id' => $request->user_id,
                'request_id' => $request->id,
                'message' => "Your request for {$request->supply_id} has been automatically disapproved due to inactivity (over 5 days).",
                'action' => 'disapproved',
            ]);

            broadcast(new NotificationSent($notif));
            
            $count++;
        }

        $this->info("Successfully disapproved {$count} requests.");
    }
}
