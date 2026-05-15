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

        // Group by batch_id (null values will be grouped together under an empty key)
        $groupedByBatch = $pendingRequests->groupBy(function ($item) {
            return $latestBatchId = $item->batch_id ?? 'single-' . $item->id;
        });

        $totalCount = 0;
        foreach ($groupedByBatch as $key => $requests) {
            $count = $requests->count();
            $firstRequest = $requests->first();
            $isBatch = !str_starts_with($key, 'single-');
            
            foreach ($requests as $request) {
                $oldValues = $request->toArray();
                
                $request->status = 'disapproved';
                $request->save();

                // Log the action for each individual item in the audit trail
                AuditService::log(
                    'UPDATE', 
                    $request, 
                    "Automatically disapproved request due to inactivity (over 5 days)", 
                    $oldValues, 
                    $request->fresh()->toArray()
                );
            }

            // Send consolidated notification
            if ($isBatch && $count > 1) {
                $message = "Your batch request with {$count} items has been automatically disapproved due to inactivity (over 5 days).";
            } else {
                $message = "Your request for {$firstRequest->supply_id} has been automatically disapproved due to inactivity (over 5 days).";
            }

            $notif = Notification::create([
                'user_id' => $firstRequest->user_id,
                'batch_id' => $isBatch ? $key : null,
                'request_id' => $isBatch ? null : $firstRequest->id,
                'message' => $message,
                'action' => 'disapproved',
            ]);

            broadcast(new NotificationSent($notif));
            
            $totalCount += $count;
        }

        $this->info("Successfully disapproved {$totalCount} requests.");
    }
}
