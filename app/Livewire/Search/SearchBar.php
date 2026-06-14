<?php

declare(strict_types=1);

namespace App\Livewire\Search;

use App\Services\Search\SearchService;
use Illuminate\View\View;
use Livewire\Component;

class SearchBar extends Component
{
    public string $query = '';

    /** @var array<string, mixed> */
    public array $results = [];

    public bool $isOpen = false;

    public bool $isLoading = false;

    /**
     * Déclenché à chaque changement de la requête (debounce géré côté Blade).
     */
    public function updatedQuery(): void
    {
        if (mb_strlen(trim($this->query)) >= 2) {
            $this->isLoading = true;
            $this->suggest();
            $this->isOpen = true;
        } else {
            $this->results = [];
            $this->isOpen  = false;
        }

        $this->isLoading = false;
    }

    /**
     * Récupère les suggestions rapides depuis le service de recherche.
     */
    public function suggest(): void
    {
        $this->results = app(SearchService::class)->suggest($this->query);
    }

    /**
     * Redirige vers la page de résultats complets.
     */
    public function search(): void
    {
        $this->redirect(route('search.index', ['q' => $this->query]));
    }

    /**
     * Ferme le dropdown de résultats.
     */
    public function close(): void
    {
        $this->isOpen  = false;
        $this->results = [];
    }

    /**
     * Redirige vers le résultat sélectionné.
     */
    public function selectResult(string $url): void
    {
        $this->redirect($url);
    }

    public function render(): View
    {
        return view('livewire.search.search-bar');
    }
}
