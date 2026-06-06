<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Services\Web\HomeService;
use Illuminate\Contracts\View\View;

class HomeController extends Controller
{
    public function __construct(private readonly HomeService $service) {}

    /** Affiche la page d'accueil avec toutes les données dynamiques. */
    public function index(): View
    {
        $data = $this->service->getHomeData();

        return view('web.home', $data);
    }
}
