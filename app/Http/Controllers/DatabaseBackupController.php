<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class DatabaseBackupController extends Controller
{
    public function exportDatabase()
    {
        // Get database credentials from .env
        $dbHost = env('DB_HOST', '127.0.0.1');
        $dbName = env('DB_DATABASE');
        $dbUser = env('DB_USERNAME');
        $dbPass = env('DB_PASSWORD');

        // Set backup file path
        $backupFile = storage_path('backups/backup-' . date('Y-m-d_H-i-s') . '.sql');

        // Ensure the backup directory exists
        if (!file_exists(storage_path('backups'))) {
            mkdir(storage_path('backups'), 0777, true);
        }

        // Command to export database
        $command = "mysqldump --user={$dbUser} --password={$dbPass} --host={$dbHost} {$dbName} > {$backupFile}";

        // Execute the command
        $output = null;
        $resultCode = null;
        exec($command, $output, $resultCode);

        if ($resultCode === 0) {
            return response()->download($backupFile)->deleteFileAfterSend(true);
        } else {
            return response()->json(['error' => 'Database export failed!', 'code' => $resultCode]);
        }
    }
}
