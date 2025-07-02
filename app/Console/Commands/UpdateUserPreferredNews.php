<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\UserPreferredNews;
use App\Models\NewsArticle;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UpdateUserPreferredNews extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'news:update-preferred 
                            {--days=7 : Number of days to look back for articles}
                            {--limit=100 : Maximum number of articles to process per user}
                            {--user-id= : Process specific user only}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update user preferred news based on their preferences';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting user preferred news update...');
        
        $days = (int) $this->option('days');
        $limit = (int) $this->option('limit');
        $specificUserId = $this->option('user-id');

        try {
            // Get users to process
            $usersQuery = User::with(['categoryPreferences', 'sourcePreferences', 'authorPreferences']);
            
            if ($specificUserId) {
                $usersQuery->where('id', $specificUserId);
            }
            
            $users = $usersQuery->get();
            
            if ($users->isEmpty()) {
                $this->warn('No users found to process.');
                return 0;
            }

            $this->info("Processing {$users->count()} users...");
            
            $totalProcessed = 0;
            $totalAdded = 0;

            foreach ($users as $user) {
                $this->info("Processing user ID: {$user->id} ({$user->name})");
                
                $processed = $this->processUserPreferences($user, $days, $limit);
                $totalProcessed += $processed['processed'];
                $totalAdded += $processed['added'];
                
                $this->info("  - Processed: {$processed['processed']} articles");
                $this->info("  - Added: {$processed['added']} new preferences");
            }

            $this->info("Completed! Total processed: {$totalProcessed}, Total added: {$totalAdded}");
            
            // Log the results
            Log::info('User preferred news update completed', [
                'users_processed' => $users->count(),
                'total_processed' => $totalProcessed,
                'total_added' => $totalAdded,
                'days_lookback' => $days,
                'limit_per_user' => $limit
            ]);

            return 0;

        } catch (\Exception $e) {
            $this->error("Error: " . $e->getMessage());
            Log::error('User preferred news update failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return 1;
        }
    }

    /**
     * Process preferences for a specific user.
     */
    private function processUserPreferences(User $user, int $days, int $limit): array
    {
        // Get user preferences
        $categoryPreferences = $user->categoryPreferences()->pluck('preference_id');
        $sourcePreferences = $user->sourcePreferences()->pluck('preference_id');
        $authorPreferences = $user->authorPreferences()->pluck('preference_id');

        // If user has no preferences, skip
        if ($categoryPreferences->isEmpty() && $sourcePreferences->isEmpty() && $authorPreferences->isEmpty()) {
            $this->warn("  - User has no preferences, skipping...");
            return ['processed' => 0, 'added' => 0];
        }

        // Build query for matching articles
        $articlesQuery = NewsArticle::where(function ($query) use ($categoryPreferences, $sourcePreferences, $authorPreferences) {
            if ($categoryPreferences->isNotEmpty()) {
                $query->orWhereIn('category_id', $categoryPreferences);
            }
            if ($sourcePreferences->isNotEmpty()) {
                $query->orWhereIn('source_id', $sourcePreferences);
            }
            if ($authorPreferences->isNotEmpty()) {
                $query->orWhereIn('author_id', $authorPreferences);
            }
        })
        ->where('created_at', '>=', now()->subDays($days))
        ->whereNotExists(function ($query) use ($user) {
            $query->select(DB::raw(1))
                  ->from('user_preferred_news')
                  ->whereColumn('user_preferred_news.article_id', 'news_articles.id')
                  ->where('user_preferred_news.user_id', $user->id);
        })
        ->orderBy('created_at', 'desc')
        ->limit($limit);

        $articles = $articlesQuery->get();
        $processed = $articles->count();
        $added = 0;

        // Add articles to user's preferred news
        foreach ($articles as $article) {
            try {
                UserPreferredNews::create([
                    'user_id' => $user->id,
                    'article_id' => $article->id,
                    'is_read' => false,
                ]);
                $added++;
            } catch (\Exception $e) {
                // Log duplicate key errors (shouldn't happen due to whereNotExists, but just in case)
                if (str_contains($e->getMessage(), 'Duplicate entry')) {
                    $this->warn("  - Duplicate entry for article {$article->id}, skipping...");
                } else {
                    $this->error("  - Error adding article {$article->id}: " . $e->getMessage());
                }
            }
        }

        return ['processed' => $processed, 'added' => $added];
    }

    /**
     * Clean up old preferred news entries.
     */
    public function cleanupOldEntries(int $daysToKeep = 30)
    {
        $this->info("Cleaning up preferred news older than {$daysToKeep} days...");
        
        $deleted = UserPreferredNews::where('created_at', '<', now()->subDays($daysToKeep))->delete();
        
        $this->info("Deleted {$deleted} old entries.");
        
        return $deleted;
    }
}
