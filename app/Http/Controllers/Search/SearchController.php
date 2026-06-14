<?php

declare(strict_types=1);

namespace App\Http\Controllers\Search;

use App\Http\Controllers\Controller;
use App\Services\Search\SearchService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SearchController extends Controller
{
    public function __construct(private readonly SearchService $searchService) {}

    /**
     * Affiche la page de résultats de recherche globale.
     */
    public function index(Request $request): View
    {
        $query = (string) $request->query('q', '');
        $type  = (string) $request->query('type', 'all');

        $search  = $this->searchService->search($query, $type);
        $results = $search['results'];
        $total   = $search['total'];

        return view('web.search.index', compact('results', 'query', 'type', 'total'));
    }
}
