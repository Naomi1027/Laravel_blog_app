<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Slack\BlockKit\Blocks\SectionBlock;
use Illuminate\Notifications\Slack\SlackMessage;
use App\Models\Article;

class SlackNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @param Collection<Article> $articles
     */
    public function __construct(
        protected Collection $articles
    ) {}

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['slack'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return SlackMessage
     */
    public function toSlack(): SlackMessage
    {
        if ($this->articles->isEmpty()) {

            return (new SlackMessage())
                ->headerBlock('本日のブログ作成はありませんでした');
        }
        return (new SlackMessage())
            ->headerBlock('本日のブログ作成数：'. $this->articles->count().'件')
            ->dividerBlock()
            ->sectionBlock(function (SectionBlock $block) {
                $block->text('作成された記事のタイトル');
            })
            ->sectionBlock(function (SectionBlock $block) {
                $messages = '';
                foreach ($this->articles as $article) {
                    $messages = $messages . "\n". $article->user()->value('display_name').':'. $article->title;
                }
                $block->text($messages);
            });
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage())
            ->line('The introduction to the notification.')
            ->action('Notification Action', url('/'))
            ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
