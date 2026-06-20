<?php

namespace App\View\Components\Web;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Footer extends Component
{
    /** @var array<string, string|null> */
    public array $social;

    public function __construct()
    {
        $this->social = config('vitrine.social');
    }

    public function render(): View
    {
        return view('components.web.footer');
    }
}
