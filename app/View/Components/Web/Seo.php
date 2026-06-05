<?php

namespace App\View\Components\Web;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Seo extends Component
{
    public string $title;
    public string $description;
    public string $image;
    public string $canonical;
    public string $type;
    public string $siteName;
    public ?string $robots;

    public function __construct(
        ?string $title = null,
        ?string $description = null,
        ?string $image = null,
        ?string $canonical = null,
        string $type = 'website',
        ?string $robots = null,
    ) {
        $this->title       = $title       ?: config('vitrine.default_title');
        $this->description = $description ?: config('vitrine.default_description');
        $this->image       = $image       ?: asset(config('vitrine.og_image'));
        $this->canonical   = $canonical   ?: url()->current();
        $this->type        = $type;
        $this->siteName    = config('app.name', 'Laravel CI');
        $this->robots      = $robots;
    }

    public function render(): View
    {
        return view('components.web.seo');
    }
}
