<?php

use App\Models\SupplyRequest;
use Illuminate\Support\Facades\Artisan;
use Carbon\Carbon;

/**
 * Test Script for Auto-Disapprove Logic
 * Run this using: php artisan tinker tinker/test_auto_disapprove.php
 */

// 1. Find the first available batch of requests
$batchId = SupplyRequest::whereNotNull('batch_id')->value('batch_id');

if (!$batchId) {
    echo "No batch requests found in the database to test with.\n";
    exit;
}

echo "Testing with Batch ID: $batchId\n";

$requests = SupplyRequest::where('batch_id', $batchId)->get();

// 2. Set updated_at to 6 days ago and status to 'approved'
foreach ($requests as $request) {
    $request->status = 'approved';
    $request->updated_at = Carbon::now()->subDays(6);
    
    // Disable timestamps so Laravel doesn't override our updated_at with now() on save
    $request->timestamps = false; 
    $request->save();
}

echo "Set " . count($requests) . " requests in batch $batchId to 'approved' and updated 6 days ago.\n";

// 3. Run the auto-disapprove command
echo "Running requests:auto-disapprove...\n";
Artisan::call('requests:auto-disapprove');
echo Artisan::output();

// 4. Verify results
$updatedRequests = SupplyRequest::where('batch_id', $batchId)->get();
$allDisapproved = $updatedRequests->every(fn($r) => $r->status === 'disapproved');

if ($allDisapproved) {
    echo "\nSUCCESS: All requests in the batch are now disapproved!\n";
} else {
    echo "\nFAILED: Not all requests in the batch were disapproved. Current statuses:\n";
    foreach($updatedRequests as $r) {
        echo "- ID {$r->id}: Status {$r->status}, Updated At: {$r->updated_at}\n";
    }
}
