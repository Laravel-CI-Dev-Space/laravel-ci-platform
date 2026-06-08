<?php

namespace App\View\Components\Web;

use App\Models\VitrineSetting;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Footer extends Component
{
    /** @var array<int, array{platform: string, url: string}> */
    public array $social;

    /** @var array<string, string> */
    public array $socialIconClasses = [
        'github'    => 'fa-brands fa-github',
        'linkedin'  => 'fa-brands fa-linkedin-in',
        'whatsapp'  => 'fa-brands fa-whatsapp',
        'twitter'   => 'fa-brands fa-x-twitter',
        'facebook'  => 'fa-brands fa-facebook-f',
        'youtube'   => 'fa-brands fa-youtube',
        'instagram' => 'fa-brands fa-instagram',
        'discord'   => 'fa-brands fa-discord',
    ];

    /** @var array<int, array{label: string, route: string}> */
    public array $quickLinks;

    /** @var array<int, array{label: string, route: string, url?: string, external?: bool}> */
    public array $communityLinks;

    public string $siteName;

    public ?string $whatsappUrl;

    public function __construct()
    {
        $globalSettings = VitrineSetting::getGroup('global');
        $links        = $globalSettings['global_social_links'] ?? [];
        $this->social = is_array($links) ? $links : (json_decode($links, true) ?: []);
        $this->siteName = config('app.name', 'Laravel CI');

        $githubUrl        = $this->findUrl('github');
        $this->whatsappUrl = $this->findUrl('whatsapp');

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

        if ($githubUrl) {
            $this->communityLinks[] = [
                'label'    => 'Contribute on GitHub',
                'url'      => $githubUrl,
                'external' => true,
            ];
        }
    }

    private function findUrl(string $platform): ?string
    {
        foreach ($this->social as $link) {
            if (($link['platform'] ?? '') === $platform && ! empty($link['url'])) {
                return $link['url'];
            }
        }

        return null;
    }

    public function render(): View
    {
        return view('components.web.footer');
    }
}
