<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Services\Web\AboutService;
use Illuminate\Contracts\View\View;

class AboutController extends Controller
{
    public function __construct(private readonly AboutService $service) {}

    /** Affiche la page À propos avec toutes les données dynamiques. */
    public function index(): View
    {
        $data = $this->service->getAboutData();

        return view('web.about', $data);
    }
}
