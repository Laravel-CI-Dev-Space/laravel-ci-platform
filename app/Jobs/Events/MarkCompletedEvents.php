<?php

declare(strict_types=1);

namespace App\Jobs\Events;

use App\Models\Event;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class MarkCompletedEvents implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Marque comme terminés tous les événements publiés dont la date de fin est passée.
     */
    public function handle(): void
    {
        Event::published()
            ->where('ends_at', '<', now())
            ->update(['status' => 'completed']);
    }
}
