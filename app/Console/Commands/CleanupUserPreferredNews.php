<?php

namespace App\Console\Commands;

use App\Models\UserPreferredNews;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CleanupUserPreferredNews extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'news:cleanup-preferred 
                            {--days=30 : Number of days to keep entries}
                            {--dry-run : Show what would be deleted without actually deleting}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean up old user preferred news entries';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $days = (int) $this->option('days');
        $dryRun = $this->option('dry-run');

        $this->info('Starting cleanup of user preferred news...');
        $this->info("Keeping entries from the last {$days} days");

        try {
            $cutoffDate = now()->subDays($days);
            
            // Count entries that would be deleted
            $entriesToDelete = UserPreferredNews::where('created_at', '<', $cutoffDate)->count();
            
            if ($entriesToDelete === 0) {
                $this->info('No old entries found to delete.');
                return 0;
            }

            $this->info("Found {$entriesToDelete} entries to delete (older than {$cutoffDate})");

            if ($dryRun) {
                $this->warn('DRY RUN: No entries will be deleted.');
                $this->info("Would delete {$entriesToDelete} entries.");
                return 0;
            }

            // Confirm deletion
            if (!$this->confirm("Are you sure you want to delete {$entriesToDelete} entries?")) {
                $this->info('Cleanup cancelled.');
                return 0;
            }

            // Delete old entries
            $deleted = UserPreferredNews::where('created_at', '<', $cutoffDate)->delete();

            $this->info("Successfully deleted {$deleted} entries.");

            // Log the cleanup
            Log::info('User preferred news cleanup completed', [
                'entries_deleted' => $deleted,
                'days_kept' => $days,
                'cutoff_date' => $cutoffDate
            ]);

            return 0;

        } catch (\Exception $e) {
            $this->error("Error: " . $e->getMessage());
            Log::error('User preferred news cleanup failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return 1;
        }
    }
}
