<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use App\Models\AdminAudit;

/**
 * Test Script for Automated Database Backup
 * Run this using: php artisan tinker tinker/test_auto_backup.php
 */

echo "--- Starting Auto Backup Test ---\n";

$backupDir = storage_path("app/backups");
$beforeFiles = File::exists($backupDir) ? File::files($backupDir) : [];
$beforeCount = count($beforeFiles);

echo "Current backup directory: $backupDir\n";
echo "Files before test: $beforeCount\n";

// 1. Run the backup command
echo "\nExecuting app:backup-database...\n";
$exitCode = Artisan::call('app:backup-database');
echo Artisan::output();

if ($exitCode === 0) {
    echo "SUCCESS: Command executed successfully.\n";
} else {
    echo "FAILED: Command exited with code $exitCode. Make sure 'mysqldump' is installed and in your system PATH.\n";
}

// 2. Verify file creation
$afterFiles = File::exists($backupDir) ? File::files($backupDir) : [];
$afterCount = count($afterFiles);

if ($afterCount > $beforeCount) {
    // Find the newest file
    usort($afterFiles, function($a, $b) {
        return $b->getMTime() <=> $a->getMTime();
    });
    $newestFile = $afterFiles[0];
    echo "VERIFIED: New backup file created: " . $newestFile->getFilename() . " (" . round($newestFile->getSize() / 1024, 2) . " KB)\n";
} else if ($exitCode === 0) {
    echo "WARNING: Command reported success but no new file was found in $backupDir.\n";
}

// 3. Verify Audit Log
$latestAudit = AdminAudit::where('action_type', 'SYSTEM_BACKUP')->latest('performed_at')->first();

if ($latestAudit && $latestAudit->performed_at->diffInMinutes(now()) < 2) {
    echo "VERIFIED: Audit log entry created: {$latestAudit->description}\n";
} else {
    echo "FAILED: No recent 'SYSTEM_BACKUP' audit log found.\n";
}

echo "\n--- Test Complete ---\n";
