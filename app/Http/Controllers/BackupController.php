<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;
use Carbon\Carbon;
use ZipArchive;
use RecursiveIteratorIterator;
use RecursiveDirectoryIterator;

class BackupController extends Controller
{
    protected $backupPath;
    
    public function __construct()
    {
        $this->backupPath = storage_path('backups');
        
        // Ensure backup directory exists
        if (!File::exists($this->backupPath)) {
            File::makeDirectory($this->backupPath, 0755, true);
        }
    }
    
    /**
     * Display backup management page
     */
    public function index()
    {
        $backupFiles = $this->getBackupFiles();
        
        // Get storage information
        $storageInfo = $this->getStorageInfo();
        
        return view('backend.backup.index', compact('backupFiles', 'storageInfo'));
    }
    
    /**
     * Get storage information
     */
    private function getStorageInfo()
    {
        $total = disk_total_space(storage_path());
        $free = disk_free_space(storage_path());
        $used = $total - $free;
        
        return [
            'total_space' => round($total / 1024 / 1024, 2),
            'free_space' => round($free / 1024 / 1024, 2),
            'used_space' => round($used / 1024 / 1024, 2),
            'used_percentage' => $total > 0 ? round(($used / $total) * 100, 2) : 0,
        ];
    }
    
    /**
     * Create a new backup
     */
    public function createBackup(Request $request)
    {
        $request->validate([
            'type' => 'required|in:full,database,files',
            'description' => 'nullable|string|max:500',
        ]);
        
        try {
            $type = $request->input('type');
            $description = $request->input('description', '');
            
            // Generate backup name
            $timestamp = Carbon::now()->format('Y-m-d_His');
            $backupName = "backup_{$timestamp}_{$type}";
            
            // Create backup
            $backupPath = $this->backupPath . '/' . $backupName;
            File::makeDirectory($backupPath, 0755, true);
            
            // Perform backup based on type
            if (in_array($type, ['full', 'database'])) {
                $this->backupDatabase($backupName, $backupPath);
            }
            
            if (in_array($type, ['full', 'files'])) {
                $this->backupFiles($backupName, $backupPath);
            }
            
            // Create metadata
            $metadata = [
                'name' => $backupName,
                'type' => $type,
                'description' => $description,
                'created_at' => now()->toDateTimeString(),
                'created_by' => auth()->id(),
                'size' => $this->getDirectorySize($backupPath),
                'files_count' => $this->countFiles($backupPath),
            ];
            
            // Save metadata
            file_put_contents(
                $backupPath . '/metadata.json',
                json_encode($metadata, JSON_PRETTY_PRINT)
            );
            
            // Create success indicator
            touch($backupPath . '/.backup_complete');
            
            Log::info('Backup created successfully', [
                'backup_name' => $backupName,
                'type' => $type,
                'size' => $metadata['size'],
                'user_id' => auth()->id()
            ]);
            
            return redirect()->route('backend.backup.index')
                ->with('success', "Backup created successfully! Name: {$backupName}");
                
        } catch (\Exception $e) {
            Log::error('Backup creation failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->route('backend.backup.index')
                ->with('error', 'Backup failed: ' . $e->getMessage());
        }
    }
    
    /**
     * Backup database
     */
    private function backupDatabase($backupName, $backupPath)
    {
        $config = config('database.connections.' . config('database.default'));
        
        if ($config['driver'] !== 'mysql') {
            throw new \Exception('Only MySQL database is supported for backup');
        }
        
        $sqlFile = $backupPath . '/database.sql';
        
        // Build mysqldump command
        $command = sprintf(
            'mysqldump --user=%s --password=%s --host=%s --port=%s %s > %s 2>&1',
            escapeshellarg($config['username']),
            escapeshellarg($config['password']),
            escapeshellarg($config['host']),
            escapeshellarg($config['port']),
            escapeshellarg($config['database']),
            escapeshellarg($sqlFile)
        );
        
        // Execute command
        exec($command, $output, $returnVar);
        
        if ($returnVar !== 0 || !file_exists($sqlFile) || filesize($sqlFile) === 0) {
            throw new \Exception('Database backup failed: ' . implode("\n", $output));
        }
        
        // Compress SQL file
        $this->compressFile($sqlFile);
    }
    
    /**
     * Backup important files
     */
    private function backupFiles($backupName, $backupPath)
    {
        $filesToBackup = [
            'storage/app' => storage_path('app'),
            'storage/logs' => storage_path('logs'),
            'storage/framework' => storage_path('framework'),
            'public/uploads' => public_path('uploads'),
        ];
        
        foreach ($filesToBackup as $relativePath => $sourcePath) {
            if (File::exists($sourcePath)) {
                $destination = $backupPath . '/' . $relativePath;
                File::makeDirectory(dirname($destination), 0755, true, true);
                
                if (File::isDirectory($sourcePath)) {
                    $this->copyDirectory($sourcePath, $destination);
                } else {
                    File::copy($sourcePath, $destination);
                }
            }
        }
    }
    
    /**
     * Copy directory recursively
     */
    private function copyDirectory($source, $destination)
    {
        if (!File::exists($destination)) {
            File::makeDirectory($destination, 0755, true);
        }
        
        $items = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($source, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::SELF_FIRST
        );
        
        foreach ($items as $item) {
            $target = $destination . '/' . $items->getSubPathName();
            
            if ($item->isDir()) {
                if (!File::exists($target)) {
                    File::makeDirectory($target, 0755, true);
                }
            } else {
                File::copy($item->getPathname(), $target);
            }
        }
    }
    
    /**
     * Compress file
     */
    private function compressFile($filePath)
    {
        if (!extension_loaded('zlib')) {
            return;
        }
        
        $compressed = gzcompress(file_get_contents($filePath), 9);
        file_put_contents($filePath . '.gz', $compressed);
        unlink($filePath);
    }
    
    /**
     * Get directory size in MB
     */
    private function getDirectorySize($path)
    {
        $size = 0;
        
        if (!File::exists($path)) {
            return 0;
        }
        
        foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path)) as $file) {
            if ($file->isFile()) {
                $size += $file->getSize();
            }
        }
        
        return round($size / 1024 / 1024, 2); // MB
    }
    
    /**
     * Count files in directory
     */
    private function countFiles($path)
    {
        $count = 0;
        
        if (!File::exists($path)) {
            return 0;
        }
        
        foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path)) as $file) {
            if ($file->isFile()) {
                $count++;
            }
        }
        
        return $count;
    }
    
    /**
     * Get list of backup files
     */
    private function getBackupFiles()
    {
        $backups = [];
        
        if (!File::exists($this->backupPath)) {
            return $backups;
        }
        
        $directories = array_filter(glob($this->backupPath . '/*'), 'is_dir');
        
        foreach ($directories as $dir) {
            $backupName = basename($dir);
            $metadataFile = $dir . '/metadata.json';
            
            if (File::exists($metadataFile) && File::exists($dir . '/.backup_complete')) {
                $metadata = json_decode(File::get($metadataFile), true);
                
                if ($metadata) {
                    $createdAt = Carbon::parse($metadata['created_at']);
                    
                    $backups[] = [
                        'name' => $backupName,
                        'type' => $metadata['type'] ?? 'unknown',
                        'description' => $metadata['description'] ?? '',
                        'size' => $metadata['size'] ?? $this->getDirectorySize($dir),
                        'created_at' => $metadata['created_at'],
                        'created_at_formatted' => $createdAt->format('M d, Y h:i A'),
                        'age_days' => $createdAt->diffInDays(),
                        'created_by' => $metadata['created_by'] ?? 'System',
                        'files_count' => $metadata['files_count'] ?? 0,
                        'path' => $dir,
                        'is_complete' => File::exists($dir . '/.backup_complete'),
                    ];
                }
            }
        }
        
        // Sort by creation date (newest first)
        usort($backups, function ($a, $b) {
            return strtotime($b['created_at']) - strtotime($a['created_at']);
        });
        
        return $backups;
    }
    
    /**
     * Download a backup
     */
    public function downloadBackup($backupName)
    {
        try {
            $backupDir = $this->backupPath . '/' . $backupName;
            
            if (!File::exists($backupDir)) {
                throw new \Exception('Backup not found');
            }
            
            // Create zip file
            $zipFile = $this->backupPath . '/' . $backupName . '.zip';
            $this->createZipArchive($backupDir, $zipFile);
            
            if (!File::exists($zipFile)) {
                throw new \Exception('Failed to create zip file');
            }
            
            // Set headers for download
            $headers = [
                'Content-Type' => 'application/zip',
                'Content-Disposition' => 'attachment; filename="' . $backupName . '.zip"',
                'Content-Length' => filesize($zipFile),
            ];
            
            // Clean up zip file after sending
            return response()->download($zipFile, $backupName . '.zip', $headers)
                ->deleteFileAfterSend(true);
                
        } catch (\Exception $e) {
            Log::error('Backup download failed', [
                'backup_name' => $backupName,
                'error' => $e->getMessage()
            ]);
            
            return redirect()->route('backend.backup.index')
                ->with('error', 'Download failed: ' . $e->getMessage());
        }
    }
    
    /**
     * Create zip archive
     */
    private function createZipArchive($source, $destination)
    {
        $zip = new ZipArchive();
        
        if ($zip->open($destination, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== TRUE) {
            throw new \Exception('Cannot create zip file');
        }
        
        $source = realpath($source);
        
        if (is_dir($source)) {
            $files = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($source, RecursiveDirectoryIterator::SKIP_DOTS),
                RecursiveIteratorIterator::SELF_FIRST
            );
            
            foreach ($files as $file) {
                $file = realpath($file);
                $relativePath = substr($file, strlen($source) + 1);
                
                if (is_dir($file)) {
                    $zip->addEmptyDir($relativePath);
                } elseif (is_file($file)) {
                    $zip->addFile($file, $relativePath);
                }
            }
        } elseif (is_file($source)) {
            $zip->addFile($source, basename($source));
        }
        
        $zip->close();
    }
    
    /**
     * Restore from backup
     */
    public function restoreBackup(Request $request, $backupName)
    {
        $request->validate([
            'restore_type' => 'required|in:full,database,files',
            'confirm' => 'required|accepted',
            'backup_current' => 'nullable|boolean',
        ]);
        
        try {
            $backupDir = $this->backupPath . '/' . $backupName;
            
            if (!File::exists($backupDir)) {
                throw new \Exception('Backup not found');
            }
            
            // Check if backup is complete
            if (!File::exists($backupDir . '/.backup_complete')) {
                throw new \Exception('Backup appears to be incomplete or corrupted');
            }
            
            // Create backup of current state if requested
            if ($request->boolean('backup_current')) {
                $this->createBackup(new Request([
                    'type' => 'full',
                    'description' => 'Pre-restore backup of current state',
                ]));
            }
            
            $restoreType = $request->input('restore_type');
            
            Log::info('Starting backup restore', [
                'backup_name' => $backupName,
                'restore_type' => $restoreType,
                'user_id' => auth()->id()
            ]);
            
            // Put application in maintenance mode
            Artisan::call('down', [
                '--retry' => 60,
                '--secret' => 'restore-' . time(),
            ]);
            
            // Restore based on type
            if (in_array($restoreType, ['full', 'database'])) {
                $this->restoreDatabase($backupDir);
            }
            
            if (in_array($restoreType, ['full', 'files'])) {
                $this->restoreFiles($backupDir);
            }
            
            // Clear caches
            Artisan::call('cache:clear');
            Artisan::call('config:clear');
            Artisan::call('view:clear');
            
            // Bring application back up
            Artisan::call('up');
            
            Log::info('Backup restore completed successfully', [
                'backup_name' => $backupName,
                'restore_type' => $restoreType,
                'user_id' => auth()->id()
            ]);
            
            return redirect()->route('backend.backup.index')
                ->with('success', 'Backup restored successfully! The system has been restarted.');
                
        } catch (\Exception $e) {
            // Ensure system comes back up even if restore fails
            Artisan::call('up');
            
            Log::error('Backup restore failed', [
                'backup_name' => $backupName,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->route('backend.backup.index')
                ->with('error', 'Restore failed: ' . $e->getMessage());
        }
    }
    
    /**
     * Restore database
     */
    private function restoreDatabase($backupDir)
    {
        $config = config('database.connections.' . config('database.default'));
        
        if ($config['driver'] !== 'mysql') {
            throw new \Exception('Only MySQL database is supported for restore');
        }
        
        $sqlFile = $backupDir . '/database.sql.gz';
        
        if (!File::exists($sqlFile)) {
            $sqlFile = $backupDir . '/database.sql';
        }
        
        if (!File::exists($sqlFile)) {
            throw new \Exception('Database backup file not found');
        }
        
        // Decompress if gzipped
        if (pathinfo($sqlFile, PATHINFO_EXTENSION) === 'gz') {
            $compressed = File::get($sqlFile);
            $sqlContent = gzuncompress($compressed);
            $tempFile = tempnam(sys_get_temp_dir(), 'restore_');
            file_put_contents($tempFile, $sqlContent);
            $sqlFile = $tempFile;
        }
        
        // Import database
        $command = sprintf(
            'mysql --user=%s --password=%s --host=%s --port=%s %s < %s 2>&1',
            escapeshellarg($config['username']),
            escapeshellarg($config['password']),
            escapeshellarg($config['host']),
            escapeshellarg($config['port']),
            escapeshellarg($config['database']),
            escapeshellarg($sqlFile)
        );
        
        exec($command, $output, $returnVar);
        
        // Clean up temp file if created
        if (isset($tempFile) && File::exists($tempFile)) {
            unlink($tempFile);
        }
        
        if ($returnVar !== 0) {
            throw new \Exception('Database restore failed: ' . implode("\n", $output));
        }
    }
    
    /**
     * Restore files
     */
    private function restoreFiles($backupDir)
    {
        $filesToRestore = [
            'storage/app' => storage_path('app'),
            'storage/logs' => storage_path('logs'),
            'storage/framework' => storage_path('framework'),
            'public/uploads' => public_path('uploads'),
        ];
        
        foreach ($filesToRestore as $backupSubDir => $restorePath) {
            $sourceDir = $backupDir . '/' . $backupSubDir;
            
            if (File::exists($sourceDir)) {
                // Backup existing files first
                if (File::exists($restorePath)) {
                    $backupExisting = $restorePath . '_backup_' . time();
                    File::move($restorePath, $backupExisting);
                }
                
                // Restore files
                $this->copyDirectory($sourceDir, $restorePath);
            }
        }
    }
    
    /**
     * Delete a backup
     */
    public function deleteBackup($backupName)
    {
        try {
            $backupDir = $this->backupPath . '/' . $backupName;
            
            if (!File::exists($backupDir)) {
                throw new \Exception('Backup not found');
            }
            
            // Delete directory recursively
            File::deleteDirectory($backupDir);
            
            // Also delete any zip file
            $zipFile = $this->backupPath . '/' . $backupName . '.zip';
            if (File::exists($zipFile)) {
                File::delete($zipFile);
            }
            
            Log::info('Backup deleted', [
                'backup_name' => $backupName,
                'user_id' => auth()->id()
            ]);
            
            return redirect()->route('backend.backup.index')
                ->with('success', 'Backup deleted successfully!');
                
        } catch (\Exception $e) {
            Log::error('Backup deletion failed', [
                'backup_name' => $backupName,
                'error' => $e->getMessage()
            ]);
            
            return redirect()->route('backend.backup.index')
                ->with('error', 'Deletion failed: ' . $e->getMessage());
        }
    }
    
    /**
     * Get system information
     */
    public function systemInfo()
    {
        $info = [
            'application' => [
                'name' => config('app.name'),
                'env' => config('app.env'),
                'debug' => config('app.debug'),
                'url' => config('app.url'),
                'timezone' => config('app.timezone'),
            ],
            'server' => [
                'php_version' => phpversion(),
                'server_software' => $_SERVER['SERVER_SOFTWARE'] ?? 'N/A',
                'server_name' => $_SERVER['SERVER_NAME'] ?? 'N/A',
                'server_addr' => $_SERVER['SERVER_ADDR'] ?? 'N/A',
                'server_port' => $_SERVER['SERVER_PORT'] ?? 'N/A',
            ],
            'database' => [
                'driver' => config('database.default'),
                'name' => config('database.connections.' . config('database.default') . '.database'),
                'host' => config('database.connections.' . config('database.default') . '.host'),
            ],
            'laravel' => [
                'version' => app()->version(),
                'locale' => config('app.locale'),
                'fallback_locale' => config('app.fallback_locale'),
            ],
            'backup' => [
                'total_backups' => count($this->getBackupFiles()),
                'backup_directory' => $this->backupPath,
                'backup_directory_size' => $this->getDirectorySize($this->backupPath) . ' MB',
                'last_backup' => $this->getLastBackupDate(),
            ],
            'storage' => $this->getStorageInfo(),
        ];
        
        return response()->json($info);
    }
    
    /**
     * Get last backup date
     */
    private function getLastBackupDate()
    {
        $backups = $this->getBackupFiles();
        
        if (empty($backups)) {
            return 'No backups found';
        }
        
        return $backups[0]['created_at_formatted'];
    }
    
    /**
     * Check if mysqldump is available
     */
    public function checkRequirements()
    {
        $checks = [
            'mysqldump' => $this->checkMysqldump(),
            'zip_extension' => extension_loaded('zip'),
            'zlib_extension' => extension_loaded('zlib'),
            'backup_directory' => File::exists($this->backupPath) && File::isWritable($this->backupPath),
            'storage_writable' => File::isWritable(storage_path()),
        ];
        
        return response()->json([
            'requirements' => $checks,
            'all_passed' => !in_array(false, $checks, true),
        ]);
    }
    
    /**
     * Check if mysqldump is available
     */
    private function checkMysqldump()
    {
        exec('which mysqldump', $output, $returnVar);
        return $returnVar === 0;
    }
}