<?php

namespace App\Console\Commands;

use App\Services\Events\EventReminderService;
use Illuminate\Console\Command;

class SendEventRemindersCommand extends Command
{
    protected $signature = 'events:send-reminders';

    protected $description = 'Send due event reminder emails to opted-in registrants (J-7, J-1, H-1)';

    public function handle(EventReminderService $reminderService): int
    {
        $count = $reminderService->sendDueReminders();

        $this->info("{$count} relance(s) envoyée(s).");

        return self::SUCCESS;
    }
}
