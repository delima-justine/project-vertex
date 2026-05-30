<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\SupplyRequest;
use App\Models\Notification;
use App\Services\AuditService;
use App\Events\NotificationSent;
use App\Mail\SupplyRequestSlip;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

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
    protected $description = 'Automatically disapprove approved requests older than 5 days';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $cutoffDate = Carbon::now()->subDays(5);

        $approvedRequests = SupplyRequest::where('status', 'approved')
            ->where('updated_at', '<=', $cutoffDate)
            ->get();

        if ($approvedRequests->isEmpty()) {
            $this->info('No approved requests found older than 5 days.');
            return;
        }

        // Group by batch_id (null values will be grouped together under an empty key)
        $groupedByBatch = $approvedRequests->groupBy(function ($item) {
            return $item->batch_id ?? 'single-' . $item->id;
        });

        $totalCount = 0;
        foreach ($groupedByBatch as $key => $requests) {
            $count = $requests->count();
            $firstRequest = $requests->first();
            $isBatch = !str_starts_with($key, 'single-');
            $updatedRequests = [];
            
            foreach ($requests as $request) {
                $oldValues = $request->toArray();
                
                $request->status = 'disapproved';
                $request->save();

                // Log the action for each individual item in the audit trail
                AuditService::log(
                    'UPDATE', 
                    $request, 
                    "Automatically disapproved request due to not being claimed within 5 days of approval", 
                    $oldValues, 
                    $request->fresh()->toArray()
                );

                $updatedRequests[] = $request->load(['user', 'supply']);
            }

            // Send consolidated notification
            if ($isBatch && $count > 1) {
                $message = "Your batch request with {$count} items has been automatically disapproved because it was not claimed within 5 days of approval.";
            } else {
                $message = "Your request for {$firstRequest->supply_id} has been automatically disapproved because it was not claimed within 5 days of approval.";
            }

            $notif = Notification::create([
                'user_id' => $firstRequest->user_id,
                'batch_id' => $isBatch ? $key : null,
                'request_id' => $isBatch ? null : $firstRequest->id,
                'message' => $message,
                'action' => 'disapproved',
            ]);

            broadcast(new NotificationSent($notif));
            
            // Send email notification
            if (count($updatedRequests) > 0) {
                try {
                    $user = $updatedRequests[0]->user;
                    Mail::to($user->email)->send(new SupplyRequestSlip(collect($updatedRequests), 'disapproved', $message));
                } catch (\Exception $e) {
                    Log::error("Failed to send auto-disapprove email for batch/request {$key}: " . $e->getMessage());
                }
            }

            $totalCount += $count;
        }

        $this->info("Successfully disapproved {$totalCount} requests.");
    }
}
