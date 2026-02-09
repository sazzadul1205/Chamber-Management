<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Carbon\Carbon;

class BackupController extends Controller
{
    /**
     * Display backup management page
     */
    public function index()
    {
        $backupFiles = $this->getBackupFiles();
        
        $storageInfo = [
            'total_space' => disk_total_space(storage_path()),
            'free_space' => disk_free_space(storage_path()),
            'used_space' => disk_total_space(storage_path()) - disk_free_space(storage_path()),
        ];
        
        // Convert bytes to MB
        foreach ($storageInfo as $key => $value) {
            $storageInfo[$key] = round($value / 1024 / 1024, 2);
        }
        
        return view('backend.backup.index', compact('backupFiles', 'storageInfo'));
    }
    
    /**
     * Create a new backup
     */
    public function createBackup(Request $request)
    {
        try {
            $type = $request->input('type', 'full');
            $description = $request->input('description', '');
            
            // Create backup using Laravel Backup package or custom method
            if (class_exists(\Spatie\LaravelBackup\BackupServiceProvider::class)) {
                // Using Spatie Backup package
                Artisan::call('backup:run');
            } else {
                // Custom backup method
                $this->createManualBackup($type, $description);
            }
            
            Log::info("Backup created by user ID: " . auth()->id(), [
                'type' => $type,
                'description' => $description
            ]);
            
            return redirect()->route('backend.backup.index')
                ->with('success', 'Backup created successfully!');
                
        } catch (\Exception $e) {
            Log::error("Backup creation failed: " . $e->getMessage());
            return redirect()->route('backend.backup.index')
                ->with('error', 'Failed to create backup: ' . $e->getMessage());
        }
    }
    
    /**
     * Manual backup creation method
     */
    private function createManualBackup($type, $description)
    {
        $timestamp = Carbon::now()->format('Y-m-d_H-i-s');
        $backupName = "backup_{$timestamp}_{$type}";
        
        // Create backup directory if not exists
        $backupPath = storage_path('backups');
        if (!file_exists($backupPath)) {
            mkdir($backupPath, 0755, true);
        }
        
        // Database backup
        if (in_array($type, ['full', 'database'])) {
            $this->backupDatabase($backupName);
        }
        
        // Files backup (only for full backups)
        if ($type === 'full') {
            $this->backupFiles($backupName);
        }
        
        // Save backup metadata
        $metadata = [
            'name' => $backupName,
            'type' => $type,
            'description' => $description,
            'created_at' => now()->toDateTimeString(),
            'created_by' => auth()->user()->id,
            'size' => $this->getBackupSize($backupName),
        ];
        
        file_put_contents(
            $backupPath . '/' . $backupName . '.json',
            json_encode($metadata, JSON_PRETTY_PRINT)
        );
    }
    
    /**
     * Backup database
     */
    private function backupDatabase($backupName)
    {
        $backupPath = storage_path("backups/{$backupName}");
        if (!file_exists($backupPath)) {
            mkdir($backupPath, 0755, true);
        }
        
        $config = config('database.connections.' . config('database.default'));
        
        // MySQL backup using mysqldump
        if ($config['driver'] === 'mysql') {
            $command = sprintf(
                'mysqldump --user=%s --password=%s --host=%s --port=%s %s > %s',
                escapeshellarg($config['username']),
                escapeshellarg($config['password']),
                escapeshellarg($config['host']),
                escapeshellarg($config['port']),
                escapeshellarg($config['database']),
                escapeshellarg($backupPath . '/database.sql')
            );
            
            exec($command, $output, $returnVar);
            
            if ($returnVar !== 0) {
                throw new \Exception('Database backup failed');
            }
        }
        
        // For other databases, you'd add similar logic
    }
    
    /**
     * Backup files
     */
    private function backupFiles($backupName)
    {
        $backupPath = storage_path("backups/{$backupName}");
        
        // Backup storage directory
        $storagePath = storage_path('app');
        $this->copyDirectory($storagePath, $backupPath . '/storage');
        
        // Backup public uploads
        $publicPath = public_path('uploads');
        if (file_exists($publicPath)) {
            $this->copyDirectory($publicPath, $backupPath . '/uploads');
        }
    }
    
    /**
     * Copy directory recursively
     */
    private function copyDirectory($source, $destination)
    {
        if (!file_exists($destination)) {
            mkdir($destination, 0755, true);
        }
        
        $dir = opendir($source);
        while (($file = readdir($dir)) !== false) {
            if ($file !== '.' && $file !== '..') {
                $srcFile = $source . '/' . $file;
                $destFile = $destination . '/' . $file;
                
                if (is_dir($srcFile)) {
                    $this->copyDirectory($srcFile, $destFile);
                } else {
                    copy($srcFile, $destFile);
                }
            }
        }
        closedir($dir);
    }
    
    /**
     * Get backup size
     */
    private function getBackupSize($backupName)
    {
        $backupPath = storage_path("backups/{$backupName}");
        if (!file_exists($backupPath)) {
            return 0;
        }
        
        $size = 0;
        foreach (new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($backupPath)) as $file) {
            if ($file->isFile()) {
                $size += $file->getSize();
            }
        }
        
        return round($size / 1024 / 1024, 2); // MB
    }
    
    /**
     * Get list of backup files
     */
    private function getBackupFiles()
    {
        $backupPath = storage_path('backups');
        $files = [];
        
        if (file_exists($backupPath)) {
            $directories = array_filter(glob($backupPath . '/*'), 'is_dir');
            
            foreach ($directories as $dir) {
                $backupName = basename($dir);
                $metadataFile = $backupPath . '/' . $backupName . '.json';
                
                if (file_exists($metadataFile)) {
                    $metadata = json_decode(file_get_contents($metadataFile), true);
                    if ($metadata) {
                        $metadata['path'] = $dir;
                        $metadata['size'] = $this->getBackupSize($backupName);
                        $metadata['created_at_formatted'] = Carbon::parse($metadata['created_at'])->format('M d, Y h:i A');
                        $metadata['age_days'] = Carbon::parse($metadata['created_at'])->diffInDays();
                        $files[] = $metadata;
                    }
                }
            }
            
            // Sort by creation date (newest first)
            usort($files, function ($a, $b) {
                return strtotime($b['created_at']) - strtotime($a['created_at']);
            });
        }
        
        return $files;
    }
    
    /**
     * Download a backup
     */
    public function downloadBackup($backupName)
    {
        try {
            $backupPath = storage_path("backups/{$backupName}");
            $zipFile = storage_path("backups/{$backupName}.zip");
            
            // Create zip file
            $this->createZip($backupPath, $zipFile);
            
            // Add metadata to zip
            $metadataFile = storage_path("backups/{$backupName}.json");
            if (file_exists($metadataFile)) {
                $zip = new \ZipArchive();
                if ($zip->open($zipFile, \ZipArchive::CREATE) === TRUE) {
                    $zip->addFile($metadataFile, 'metadata.json');
                    $zip->close();
                }
            }
            
            return response()->download($zipFile)->deleteFileAfterSend(true);
            
        } catch (\Exception $e) {
            Log::error("Backup download failed: " . $e->getMessage());
            return redirect()->route('backend.backup.index')
                ->with('error', 'Failed to download backup: ' . $e->getMessage());
        }
    }
    
    /**
     * Create zip archive
     */
    private function createZip($source, $destination)
    {
        $zip = new \ZipArchive();
        
        if ($zip->open($destination, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) !== TRUE) {
            throw new \Exception('Cannot create zip file');
        }
        
        $source = realpath($source);
        $files = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($source),
            \RecursiveIteratorIterator::LEAVES_ONLY
        );
        
        foreach ($files as $file) {
            if (!$file->isDir()) {
                $filePath = $file->getRealPath();
                $relativePath = substr($filePath, strlen($source) + 1);
                $zip->addFile($filePath, $relativePath);
            }
        }
        
        $zip->close();
    }
    
    /**
     * Restore from backup
     */
    public function restoreBackup(Request $request, $backupName)
    {
        try {
            $this->validate($request, [
                'restore_type' => 'required|in:full,database,files',
                'confirm' => 'required|accepted',
            ]);
            
            $restoreType = $request->input('restore_type');
            
            // Put application in maintenance mode
            Artisan::call('down');
            
            // Restore based on type
            if (in_array($restoreType, ['full', 'database'])) {
                $this->restoreDatabase($backupName);
            }
            
            if (in_array($restoreType, ['full', 'files'])) {
                $this->restoreFiles($backupName);
            }
            
            Log::info("Backup restored by user ID: " . auth()->id(), [
                'backup_name' => $backupName,
                'restore_type' => $restoreType
            ]);
            
            // Bring application back up
            Artisan::call('up');
            
            return redirect()->route('backend.backup.index')
                ->with('success', 'Backup restored successfully! The system may need a moment to update.');
                
        } catch (\Exception $e) {
            // Ensure system comes back up even if restore fails
            Artisan::call('up');
            
            Log::error("Backup restore failed: " . $e->getMessage());
            return redirect()->route('backend.backup.index')
                ->with('error', 'Failed to restore backup: ' . $e->getMessage());
        }
    }
    
    /**
     * Restore database
     */
    private function restoreDatabase($backupName)
    {
        $backupPath = storage_path("backups/{$backupName}");
        $sqlFile = $backupPath . '/database.sql';
        
        if (!file_exists($sqlFile)) {
            throw new \Exception('Database backup file not found');
        }
        
        $config = config('database.connections.' . config('database.default'));
        
        // MySQL restore
        if ($config['driver'] === 'mysql') {
            $command = sprintf(
                'mysql --user=%s --password=%s --host=%s --port=%s %s < %s',
                escapeshellarg($config['username']),
                escapeshellarg($config['password']),
                escapeshellarg($config['host']),
                escapeshellarg($config['port']),
                escapeshellarg($config['database']),
                escapeshellarg($sqlFile)
            );
            
            exec($command, $output, $returnVar);
            
            if ($returnVar !== 0) {
                throw new \Exception('Database restore failed');
            }
        }
    }
    
    /**
     * Restore files
     */
    private function restoreFiles($backupName)
    {
        $backupPath = storage_path("backups/{$backupName}");
        
        // Restore storage directory
        $storageBackup = $backupPath . '/storage';
        if (file_exists($storageBackup)) {
            $this->copyDirectory($storageBackup, storage_path('app'));
        }
        
        // Restore uploads
        $uploadsBackup = $backupPath . '/uploads';
        if (file_exists($uploadsBackup)) {
            $this->copyDirectory($uploadsBackup, public_path('uploads'));
        }
    }
    
    /**
     * Delete a backup
     */
    public function deleteBackup($backupName)
    {
        try {
            $backupPath = storage_path("backups/{$backupName}");
            $metadataFile = storage_path("backups/{$backupName}.json");
            
            // Delete backup directory
            if (file_exists($backupPath)) {
                $this->deleteDirectory($backupPath);
            }
            
            // Delete metadata file
            if (file_exists($metadataFile)) {
                unlink($metadataFile);
            }
            
            Log::info("Backup deleted by user ID: " . auth()->id(), [
                'backup_name' => $backupName
            ]);
            
            return redirect()->route('backend.backup.index')
                ->with('success', 'Backup deleted successfully!');
                
        } catch (\Exception $e) {
            Log::error("Backup deletion failed: " . $e->getMessage());
            return redirect()->route('backend.backup.index')
                ->with('error', 'Failed to delete backup: ' . $e->getMessage());
        }
    }
    
    /**
     * Delete directory recursively
     */
    private function deleteDirectory($dir)
    {
        if (!file_exists($dir)) {
            return;
        }
        
        $files = array_diff(scandir($dir), ['.', '..']);
        foreach ($files as $file) {
            $path = $dir . '/' . $file;
            is_dir($path) ? $this->deleteDirectory($path) : unlink($path);
        }
        
        rmdir($dir);
    }
    
    /**
     * Get system information
     */
    public function systemInfo()
    {
        $info = [
            'laravel_version' => app()->version(),
            'php_version' => phpversion(),
            'database' => config('database.default'),
            'timezone' => config('app.timezone'),
            'environment' => app()->environment(),
            'last_backup' => $this->getLastBackupDate(),
            'backup_count' => count($this->getBackupFiles()),
        ];
        
        return response()->json($info);
    }
    
    /**
     * Get last backup date
     */
    private function getLastBackupDate()
    {
        $files = $this->getBackupFiles();
        return !empty($files) ? $files[0]['created_at_formatted'] : 'Never';
    }
}