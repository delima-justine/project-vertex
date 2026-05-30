<?php

namespace App\Http\Controllers;

use App\Services\AuditService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class DatabaseController extends Controller
{
    public function backup()
    {
        if (!function_exists('exec')) {
            return response()->json(['message' => 'PHP exec() function is disabled on this server. Please enable it in your hosting PHP settings.'], 500);
        }

        $connection = config('database.default');
        
        if ($connection !== 'mysql') {
            return response()->json(['message' => 'Backup only supported for MySQL'], 400);
        }

        $config = config("database.connections.$connection");
        $filename = "backup-" . now()->format('Y-m-d-H-i-s') . ".sql";
        $path = storage_path("app/backups/" . $filename);

        if (!is_dir(storage_path("app/backups"))) {
            mkdir(storage_path("app/backups"), 0755, true);
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
            return response()->json(['message' => 'Backup failed. Ensure mysqldump is installed and accessible.'], 500);
        }

        AuditService::log('BACKUP', null, 'Database backup generated and downloaded.');

        return Response::download($path)->deleteFileAfterSend(true);
    }

    public function restore(Request $request)
    {
        if (!function_exists('exec')) {
            return response()->json(['message' => 'PHP exec() function is disabled on this server. Please enable it in your hosting PHP settings.'], 500);
        }

        $request->validate([
            'file' => 'required|file',
            'password' => 'required|string',
        ]);

        if (!\Illuminate\Support\Facades\Hash::check($request->password, $request->user()->password)) {
            return response()->json(['message' => 'Incorrect password. Restore aborted.'], 403);
        }

        $connection = config('database.default');
        
        if ($connection !== 'mysql') {
            return response()->json(['message' => 'Restore only supported for MySQL'], 400);
        }

        $config = config("database.connections.$connection");
        $file = $request->file('file');
        $path = $file->getRealPath();

        $password = $config['password'] ? "--password=" . escapeshellarg($config['password']) : "";

        $command = sprintf(
            'mysql --user=%s %s --host=%s %s < %s',
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
            return response()->json(['message' => 'Restore failed. Ensure mysql client is installed and accessible.'], 500);
        }

        AuditService::log('RESTORE', null, 'Database restored from uploaded file: ' . $file->getClientOriginalName());

        return response()->json(['message' => 'Database restored successfully']);
    }
}
