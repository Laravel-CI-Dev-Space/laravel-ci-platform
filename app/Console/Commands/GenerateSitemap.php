<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Spatie\Sitemap\Sitemap;
use Spatie\Sitemap\Tags\Url;

class GenerateSitemap extends Command
{
    protected $signature = 'sitemap:generate';

    protected $description = 'Generate the public XML sitemap for vitrine pages';

    public function handle(): int
    {
        $sitemap = Sitemap::create();

        foreach ($this->staticRoutes() as $routeName) {
            $sitemap->add(
                Url::create(route($routeName))
                    ->setChangeFrequency(Url::CHANGE_FREQUENCY_WEEKLY)
                    ->setPriority(0.8)
            );
        }

        $sitemap->add(
            Url::create(route('home'))
                ->setChangeFrequency(Url::CHANGE_FREQUENCY_DAILY)
                ->setPriority(1.0)
        );

        $path = public_path('sitemap.xml');
        $sitemap->writeToFile($path);

        $this->info("Sitemap written to {$path}");

        return self::SUCCESS;
    }

    /** @return list<string> */
    private function staticRoutes(): array
    {
        return [
            'about',
            'join',
            'forum.index',
            'blog.index',
            'events.index',
            'jobs.index',
        ];
    }
}
