<?php

namespace App\Console\Commands;

use App\Services\Events\EventReminderService;
use Illuminate\Console\Command;

class SendEventRemindersCommand extends Command
{
    protected $signature = 'events:send-reminders';

    protected $description = 'Envoie les relances J-7, J-1 et H-1 aux inscrits confirmés';

    public function handle(EventReminderService $reminderService): int
    {
        $count = $reminderService->sendDueReminders();

        $this->info("{$count} relance(s) envoyée(s).");

        return self::SUCCESS;
    }
}
