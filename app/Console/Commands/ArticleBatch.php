<?php

namespace App\Console\Commands;

use App\Models\Article;
use App\Notifications\SlackNotification;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Notification;

class ArticleBatch extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:article-batch';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '一日の記事作成数とタイトルを集計';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        // バッチ処理
        $articles = Article::whereDate('created_at', Carbon::today())->get();

        // Slackに通知
        Notification::route('slack', config('services.slack.notifications.channel'))
            ->notify(new SlackNotification($articles));
        $this->info('バッチ処理が完了いたしました。');
    }
}
