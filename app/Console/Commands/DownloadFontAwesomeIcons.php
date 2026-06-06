<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Services\FontAwesomeService;
use Illuminate\Console\Command;

class DownloadFontAwesomeIcons extends Command
{
    protected $signature = 'icons:download';

    protected $description = 'Download the full Font Awesome 6 Free icon catalog from GitHub and cache it locally.';

    public function handle(FontAwesomeService $service): int
    {
        $this->info('Downloading Font Awesome 6 Free icon catalog…');

        try {
            $count = $service->downloadCatalog();
            $this->info("Done! {$count} icons stored in storage/app/fontawesome/catalog.json.");
            $this->line('The icon picker will now use the full catalog.');

            return self::SUCCESS;
        } catch (\Throwable $e) {
            $this->error($e->getMessage());

            return self::FAILURE;
        }
    }
}
