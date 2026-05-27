<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use App\Services\AuditService;
use Carbon\Carbon;
use Illuminate\Support\Facades\File;

class BackupDatabase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:backup-database';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate a weekly database backup and delete files older than 30 days';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting automated database backup...');

        if (!function_exists('exec')) {
            $this->error('PHP exec() function is disabled.');
            Log::error('Automated backup failed: PHP exec() function is disabled.');
            return 1;
        }

        $connection = config('database.default');
        if ($connection !== 'mysql') {
            $this->error('Backup only supported for MySQL.');
            return 1;
        }

        $config = config("database.connections.$connection");
        $filename = "auto-backup-" . now()->format('Y-m-d-H-i-s') . ".sql";
        $backupDir = storage_path("app/backups");
        $path = $backupDir . "/" . $filename;

        if (!File::isDirectory($backupDir)) {
            File::makeDirectory($backupDir, 0755, true);
        }

        $password = $config['password'] ? "--password=" . escapeshellarg($config['password']) : "";
        
        $command = sprintf(
            'mysqldump --user=%s %s --host=%s %s > %s',
            escapeshellarg($config['username']),
            $password,
            escapeshellarg($config['host']),
            escapeshellarg($config['database']),
            escapeshellarg($path)
        );

        $returnVar = NULL;
        $output = NULL;
        \exec($command, $output, $returnVar);

        if ($returnVar !== 0) {
            $this->error('Backup failed. Check system logs.');
            Log::error('Automated backup failed for database: ' . $config['database']);
            return 1;
        }

        $this->info("Backup generated successfully: {$filename}");
        AuditService::log('SYSTEM_BACKUP', null, "Automated weekly backup generated: {$filename}");

        // Cleanup: Delete backups older than 30 days
        $this->cleanupOldBackups($backupDir);

        return 0;
    }

    /**
     * Delete backup files older than 30 days.
     */
    private function cleanupOldBackups(string $directory)
    {
        $this->info('Cleaning up old backups...');
        $files = File::files($directory);
        $now = Carbon::now();
        $deletedCount = 0;

        foreach ($files as $file) {
            // Check if it's an automated backup file (to avoid deleting manual ones if desired, 
            // but usually we want to clean all .sql files in this dir)
            if ($file->getExtension() === 'sql') {
                $lastModified = Carbon::createFromTimestamp($file->getMTime());
                
                if ($lastModified->diffInDays($now) > 30) {
                    File::delete($file->getPathname());
                    $deletedCount++;
                }
            }
        }

        if ($deletedCount > 0) {
            $this->info("Deleted {$deletedCount} old backup files.");
            Log::info("Automated cleanup: Deleted {$deletedCount} backup files older than 30 days.");
        } else {
            $this->info('No old backups found to delete.');
        }
    }
}
