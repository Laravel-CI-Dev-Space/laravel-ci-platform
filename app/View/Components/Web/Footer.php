<?php

namespace App\View\Components\Web;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Footer extends Component
{
    /** @var array<string, string|null> */
    public array $social;

    /** @var array<int, array{label: string, route: string}> */
    public array $quickLinks;

    /** @var array<int, array{label: string, route: string, url?: string, external?: bool}> */
    public array $communityLinks;

    public string $siteName;

    public function __construct()
    {
        $this->social   = config('vitrine.social');
        $this->siteName = config('app.name', 'Laravel CI');

        $this->quickLinks = [
            ['label' => 'Forum',  'route' => 'forum.index'],
            ['label' => 'Blog',   'route' => 'blog.index'],
            ['label' => 'Events', 'route' => 'events.index'],
            ['label' => 'Jobs',   'route' => 'jobs.index'],
        ];

        $this->communityLinks = [
            ['label' => 'About us',        'route' => 'about'],
            ['label' => 'Join us',         'route' => 'join'],
            ['label' => 'Code of conduct', 'route' => 'forum.index'],
        ];

        if ($this->social['github']) {
            $this->communityLinks[] = [
                'label'    => 'Contribute on GitHub',
                'url'      => $this->social['github'],
                'external' => true,
            ];
        }
    }

    public function render(): View
    {
        return view('components.web.footer');
    }
}
