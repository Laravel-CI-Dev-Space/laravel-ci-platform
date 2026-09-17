<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Mail\NewsletterArticleMail;
use App\Models\Article;
use App\Models\NewsletterSubscriber;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendNewsletterBatch implements ShouldQueue
{
    use Queueable;

    public int $timeout = 300;
    public int $tries   = 1;

    public function __construct(public readonly Article $article) {}

    public function handle(): void
    {
        $subscribers = NewsletterSubscriber::active()->get();

        foreach ($subscribers as $subscriber) {
            try {
                Mail::to($subscriber->email)
                    ->send(new NewsletterArticleMail($this->article, $subscriber));
            } catch (\Throwable $e) {
                Log::error("Newsletter: échec envoi à {$subscriber->email} — " . $e->getMessage());
            }
        }
    }
}
