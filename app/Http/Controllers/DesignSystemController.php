<?php

namespace App\Http\Controllers;

class DesignSystemController extends Controller
{
    public function index()
    {
        $event = [
            'title'       => 'Laravel CI Meetup #25',
            'date'        => '15 Oct.',
            'location'    => 'Orange Fab, Abidjan',
            'time'        => '09:00 - 17:00',
            'description' => 'Focus sur Laravel 11 et les nouvelles fonctionnalités de Reverb.',
            'image'       => null,
            'seats_taken' => 38,
            'seats_total' => 50,
            'cta_label'   => 'Réserver ma place',
            'cta_url'     => '#',
        ];

        $post = [
            'title'        => 'Optimiser vos requêtes Eloquent',
            'excerpt'      => 'Découvrez les patterns essentiels pour éviter le problème N+1 dans vos app.',
            'image'        => null,
            'category'     => 'Architecture',
            'read_time'    => 10,
            'author'       => ['name' => 'Jean-Marc Koffi', 'avatar' => null],
            'published_at' => '12 Mai 2026',
            'url'          => '#',
        ];

        $thread = [
            'title'     => 'Comment optimiser les requêtes Eloquent avec des relations polymorphes ?',
            'excerpt'   => "J'ai un souci de performance N+1 sur mon dashboard où je charge plusieurs types de notifications…",
            'status'    => 'open',
            'label'     => 'AIDE',
            'tags'      => ['Laravel 11', 'Alpine.js'],
            'votes'     => 12,
            'replies'   => 5,
            'author'    => ['name' => 'Kouassi_Dev', 'avatar' => null],
            'posted_at' => 'Il y a 2 heures',
            'url'       => '#',
        ];

        $job = [
            'title'     => 'Développeur Fullstack Senior',
            'company'   => 'Yango',
            'logo'      => null,
            'contract'  => 'CDI',
            'location'  => 'Abidjan',
            'remote'    => false,
            'stack'     => ['Laravel', 'Vue.js', 'MySQL'],
            'salary'    => null,
            'posted_at' => 'Hier',
            'url'       => '#',
        ];

        $member = [
            'username' => 'Yao_Laravel',
            'avatar'   => null,
            'points'   => 1240,
            'url'      => '#',
        ];

        $statModerne = [
            'value' => '356',
            'label' => 'Total membres actifs',
            'users' => [
                ['name' => 'Jean-Marc Koffi', 'avatar' => null],
                ['name' => 'Kouassi Dev',      'avatar' => null],
                ['name' => 'Yao Laravel',      'avatar' => null],
            ],
        ];

        $snippets = $this->snippets();

        return view('design-system', compact('event', 'post', 'thread', 'job', 'member', 'statModerne', 'snippets'));
    }

    private function snippets(): array
    {
        return [
            'stat_moderne' => <<<'SNIPPET'
<x-card.stat-moderne value="356" label="Total membres actifs" :users="$statModerne['users']">
    <x-slot:icon>
        <svg class="size-6 shrink-0" fill="none" stroke-width="1.5"
            stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round"
                d="M18 18.72a9.094 9.094 0 0 0 3.741-.479 3 3 0 0 0-4.682-2.72m.94 3.198.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0 1 12 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 0 1 6 18.719m12 0a5.971 5.971 0 0 0-.941-3.197m0 0A5.995 5.995 0 0 0 12 12.75a5.995 5.995 0 0 0-5.058 2.772m0 0a3 3 0 0 0-4.681 2.72 8.986 8.986 0 0 0 3.74.477m.94-3.197a5.971 5.971 0 0 0-.94 3.197M15 6.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm6 3a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Zm-13.5 0a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Z" />
        </svg>
    </x-slot:icon>
</x-card.stat-moderne>
SNIPPET,
            'stat' => <<<'SNIPPET'
<x-card.stat value="500+" label="Membres Actifs">
    <x-slot:icon>
        <svg class="size-6 shrink-0 text-white" fill="none" stroke-width="1.5"
            stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round"
                d="M18 18.72a9.094 9.094 0 0 0 3.741-.479 3 3 0 0 0-4.682-2.72m.94 3.198.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0 1 12 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 0 1 6 18.719m12 0a5.971 5.971 0 0 0-.941-3.197m0 0A5.995 5.995 0 0 0 12 12.75a5.995 5.995 0 0 0-5.058 2.772m0 0a3 3 0 0 0-4.681 2.72 8.986 8.986 0 0 0 3.74.477m.94-3.197a5.971 5.971 0 0 0-.94 3.197M15 6.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm6 3a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Zm-13.5 0a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Z" />
        </svg>
    </x-slot:icon>
</x-card.stat>
SNIPPET,
            'stat_sec' => '<x-card.stat-secondary icon="forum" value="1.2k" description="Messages mensuels sur le forum" />',
            'event'    => '<x-card.event :event="$event" />',
            'post'     => '<x-card.post :post="$post" />',
            'thread'   => '<x-card.forum-thread :thread="$thread" />',
            'job'      => '<x-card.job :job="$job" />',
            'member'       => '<x-card.member :member="$member" :rank="1" />',
            'avatar' => '<x-avatar name="Tom Cook" subtitle="Voir le profil" />',
            'badge'         => '<x-badge label="Meetup" />',
            'badge_rounded' => '<x-badge-rounded label="Laravel 11" color="red" />',
            'progress_bar'  => '<x-progress-bar label="38 / 50 inscrits" value="12 restante(s)" :percent="76" />',
        ];
    }
}
