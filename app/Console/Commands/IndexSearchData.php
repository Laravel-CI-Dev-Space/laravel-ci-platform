<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;

class IndexSearchData extends Command
{
    protected $signature = 'search:index';

    protected $description = 'Indexe toutes les données dans Meilisearch';

    /**
     * Indexe les questions, articles et offres d'emploi via Scout.
     */
    public function handle(): void
    {
        $this->info('Indexation des questions...');
        $this->call('scout:import', ['model' => 'App\Models\Question']);

        $this->info('Indexation des articles...');
        $this->call('scout:import', ['model' => 'App\Models\Article']);

        $this->info("Indexation des offres d'emploi...");
        $this->call('scout:import', ['model' => 'App\Models\JobOffer']);

        $this->info('✅ Indexation terminée.');
    }
}
