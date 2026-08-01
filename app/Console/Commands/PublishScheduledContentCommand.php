<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\ContentPage;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

final class PublishScheduledContentCommand extends Command
{
    /** @var string */
    protected $signature = 'cms:publish-scheduled';

    /** @var string */
    protected $description = 'Publish content pages scheduled for publication.';

    public function handle(): int
    {
        $duePages = ContentPage::query()
            ->where('status', 'scheduled')
            ->whereNotNull('scheduled_for')
            ->where('scheduled_for', '<=', now())
            ->get();

        $count = 0;

        foreach ($duePages as $page) {
            DB::transaction(function () use ($page): void {
                $before = $page->toArray();

                $page->update([
                    'status' => 'published',
                    'published_at' => now(),
                    'scheduled_for' => null,
                ]);

                $page->refresh();

                $revisionNumber = ((int) $page->revisions()->max('revision_number')) + 1;

                $page->revisions()->create([
                    'created_by' => null,
                    'revision_number' => $revisionNumber,
                    'status' => 'published',
                    'snapshot' => $page->toArray(),
                    'change_summary' => 'Scheduled publication executed.',
                ]);

                DB::table('audit_events')->insert([
                    'id' => (string) Str::uuid(),
                    'actor_id' => null,
                    'action' => 'content.page.published',
                    'subject_type' => ContentPage::class,
                    'subject_id' => (string) $page->getKey(),
                    'correlation_id' => (string) Str::uuid(),
                    'ip_address' => '127.0.0.1',
                    'user_agent' => 'Console Scheduler',
                    'before' => json_encode($before, JSON_THROW_ON_ERROR),
                    'after' => json_encode($page->toArray(), JSON_THROW_ON_ERROR),
                    'metadata' => json_encode(['source' => 'console.scheduler'], JSON_THROW_ON_ERROR),
                    'occurred_at' => now(),
                ]);
            });

            $count++;
        }

        $this->info("Published {$count} scheduled content page(s).");

        return Command::SUCCESS;
    }
}
