<?php

namespace App\View\Components\Web;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Header extends Component
{
    /** @var array<int, array{label: string, route: string, pattern: string}> */
    public array $navItems;

    public function __construct()
    {
        $this->navItems = [
            ['label' => 'Home',   'route' => 'home',         'pattern' => 'home'],
            ['label' => 'Forum',  'route' => 'forum.index',  'pattern' => 'forum.*'],
            ['label' => 'Blog',   'route' => 'blog.index',   'pattern' => 'blog.*'],
            ['label' => 'Events', 'route' => 'events.index', 'pattern' => 'events.*'],
            ['label' => 'Jobs',   'route' => 'jobs.index',   'pattern' => 'jobs.*'],
            ['label' => 'About',  'route' => 'about',        'pattern' => 'about'],
            ['label' => 'Join',   'route' => 'join',         'pattern' => 'join'],
        ];
    }

    public function render(): View
    {
        return view('components.web.header');
    }
}
