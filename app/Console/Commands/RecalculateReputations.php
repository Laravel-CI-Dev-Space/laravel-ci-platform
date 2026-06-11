<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Services\ReputationService;
use Illuminate\Console\Command;

class RecalculateReputations extends Command
{
    protected $signature = 'reputation:recalculate';

    protected $description = 'Recalculate the points and grade of every member.';

    public function handle(ReputationService $service): int
    {
        $this->info('Recalcul des points et grades en cours…');

        $service->recalculateAll();

        $this->info('Terminé.');

        return self::SUCCESS;
    }
}
