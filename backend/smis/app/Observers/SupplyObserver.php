<?php

namespace App\Observers;

use App\Models\Supply;
use App\Models\Notification;
use App\Models\User;
use App\Events\NotificationSent;

class SupplyObserver
{
    /**
     * Handle the Supply "saving" event.
     * Automatically update the status based on quantity before saving to database.
     */
    public function saving(Supply $supply): void
    {
        if ($supply->quantity == 0) {
            $supply->status = 'Out of Stock';
        } elseif ($supply->quantity <= 5) {
            $supply->status = 'Low Stock';
        } else {
            $supply->status = 'Available';
        }
    }

    /**
     * Handle the Supply "saved" event.
     * Trigger notifications when stock drops below threshold.
     */
    public function saved(Supply $supply): void
    {
        // Check if the quantity field was changed in the last save
        if ($supply->wasChanged('quantity')) {
            $newQuantity = $supply->quantity;
            $oldQuantity = $supply->getOriginal('quantity');

            // Threshold for low stock is 5
            // Trigger if new quantity is low/zero and it actually decreased
            if ($newQuantity <= 5 && $newQuantity < $oldQuantity) {
                if ($newQuantity == 0) {
                    $message = "Out of Stock Alert: {$supply->item_desc} (Stock Num: {$supply->stock_num}) is now out of stock.";
                    $action = 'out of stock';
                } else {
                    $message = "Low Stock Warning: {$supply->item_desc} (Stock Num: {$supply->stock_num}) is down to {$newQuantity} units.";
                    $action = 'low stock';
                }

                $this->notifyAdmins($supply, $message, $action);
            }
        }
    }

    /**
     * Notify all Admins and SuperAdmins.
     */
    protected function notifyAdmins(Supply $supply, string $message, string $action): void
    {
        // Find users with Admin or SuperAdmin roles
        // We check both lowercase (project standard) and Title Case (seeder standard)
        $admins = User::whereHas('role', function($query) {
            $query->whereIn('role_name', ['admin', 'superadmin', 'Admin', 'SuperAdmin']);
        })->get();

        foreach ($admins as $admin) {
            $notification = Notification::create([
                'user_id' => $admin->id,
                'action' => $action,
                'message' => $message,
                'office_id' => $admin->office_id,
            ]);

            // Broadcast the notification for real-time update
            broadcast(new NotificationSent($notification));
        }
    }
}
