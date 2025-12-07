<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class BackupDatabase extends Command
{
    protected $signature = 'db:backup 
                            {--keep=7 : Number of days to keep backups}
                            {--path=backups : Storage path for backups}';

    protected $description = 'Backup database and payment proof files';

    public function handle()
    {
        $this->info('🔄 Starting database backup...');

        try {
            // 1. Backup Database
            $this->backupDatabase();

            // 2. Backup Payment Proofs
            $this->backupFiles();

            // 3. Cleanup Old Backups
            $this->cleanupOldBackups();

            $this->info('✅ Backup completed successfully!');
            return 0;

        } catch (\Exception $e) {
            $this->error('❌ Backup failed: ' . $e->getMessage());
            \Log::error('Backup failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return 1;
        }
    }

    protected function backupDatabase()
    {
        $this->info('📊 Backing up database...');

        $timestamp = Carbon::now()->format('Y-m-d_H-i-s');
        $database = config('database.connections.mysql.database');
        $username = config('database.connections.mysql.username');
        $password = config('database.connections.mysql.password');
        $host = config('database.connections.mysql.host');

        $backupPath = $this->option('path');
        $filename = "db_backup_{$timestamp}.sql";
        $filepath = storage_path("app/{$backupPath}/{$filename}");

        // Create directory if not exists
        if (!file_exists(dirname($filepath))) {
            mkdir(dirname($filepath), 0755, true);
        }

        // Windows mysqldump command
        $command = sprintf(
            'mysqldump --user=%s --password=%s --host=%s %s > %s',
            escapeshellarg($username),
            escapeshellarg($password),
            escapeshellarg($host),
            escapeshellarg($database),
            escapeshellarg($filepath)
        );

        exec($command, $output, $returnVar);

        if ($returnVar !== 0) {
            throw new \Exception('mysqldump failed. Make sure MySQL is installed and accessible.');
        }

        // Compress the backup
        $gzPath = $filepath . '.gz';
        $fp = gzopen($gzPath, 'w9');
        gzwrite($fp, file_get_contents($filepath));
        gzclose($fp);
        unlink($filepath); // Remove uncompressed file

        $size = round(filesize($gzPath) / 1024 / 1024, 2);
        $this->info("✓ Database backed up: {$filename}.gz ({$size} MB)");

        \Log::info('Database backup created', [
            'filename' => $filename . '.gz',
            'size_mb' => $size
        ]);
    }

    protected function backupFiles()
    {
        $this->info('📁 Backing up payment proof files...');

        $timestamp = Carbon::now()->format('Y-m-d_H-i-s');
        $backupPath = $this->option('path');

        // Get payment proofs directory
        $paymentsPath = storage_path('app/public/payments');

        if (!is_dir($paymentsPath)) {
            $this->warn('⚠ No payment files to backup');
            return;
        }

        // Create ZIP archive
        $zipFile = storage_path("app/{$backupPath}/files_backup_{$timestamp}.zip");
        $zip = new \ZipArchive();

        if ($zip->open($zipFile, \ZipArchive::CREATE) !== TRUE) {
            throw new \Exception('Could not create ZIP file');
        }

        $files = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($paymentsPath),
            \RecursiveIteratorIterator::LEAVES_ONLY
        );

        $count = 0;
        foreach ($files as $file) {
            if (!$file->isDir()) {
                $filePath = $file->getRealPath();
                $relativePath = substr($filePath, strlen($paymentsPath) + 1);
                $zip->addFile($filePath, $relativePath);
                $count++;
            }
        }

        $zip->close();

        $size = round(filesize($zipFile) / 1024 / 1024, 2);
        $this->info("✓ Files backed up: {$count} files ({$size} MB)");

        \Log::info('Files backup created', [
            'count' => $count,
            'size_mb' => $size
        ]);
    }

    protected function cleanupOldBackups()
    {
        $keepDays = (int) $this->option('keep');
        $backupPath = $this->option('path');

        $this->info("🗑 Cleaning up backups older than {$keepDays} days...");

        $cutoffDate = Carbon::now()->subDays($keepDays);
        $directory = storage_path("app/{$backupPath}");

        if (!is_dir($directory)) {
            return;
        }

        $files = glob($directory . '/*');
        $deletedCount = 0;

        foreach ($files as $file) {
            if (is_file($file)) {
                $fileTime = Carbon::createFromTimestamp(filemtime($file));

                if ($fileTime->lt($cutoffDate)) {
                    unlink($file);
                    $deletedCount++;
                    $this->line("  Deleted: " . basename($file));
                }
            }
        }

        if ($deletedCount > 0) {
            $this->info("✓ Cleaned up {$deletedCount} old backup(s)");
        } else {
            $this->info("✓ No old backups to clean up");
        }
    }
}
