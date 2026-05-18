<?php

namespace App\Http\Controllers;

class DesignSystemController extends Controller
{
    public function index()
    {
        return view('design-system', [
            'event'  => $this->event(),
            'post'   => $this->post(),
            'thread' => $this->thread(),
            'job'    => $this->job(),
            'member' => $this->member(),
        ]);
    }

    private function event(): array
    {
        return [
            'title'       => 'Laravel CI Meetup #25',
            'date'        => '15 Oct.',
            'location'    => 'Orange Fab, Abidjan',
            'time'        => '09:00 - 17:00',
            'type'        => 'meetup',
            'description' => 'Focus sur Laravel 11 et les nouvelles fonctionnalités de Reverb.',
            'image'       => null,
            'seats_taken' => 38,
            'seats_total' => 50,
            'cta_label'   => 'Réserver ma place',
            'cta_url'     => '#',
        ];
    }

    private function post(): array
    {
        return [
            'title'        => 'Optimiser vos requêtes Eloquent pour les gros volumes',
            'excerpt'      => 'Découvrez les patterns essentiels pour éviter le problème N+1 dans vos applications Laravel à fort trafic.',
            'image'        => null,
            'category'     => 'Architecture',
            'read_time'    => 10,
            'author'       => ['name' => 'Jean-Marc Koffi', 'avatar' => null],
            'published_at' => '12 Mai 2026',
            'url'          => '#',
        ];
    }

    private function thread(): array
    {
        return [
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
    }

    private function job(): array
    {
        return [
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
    }

    private function member(): array
    {
        return [
            'username' => 'Yao_Laravel',
            'avatar'   => null,
            'points'   => 1240,
            'url'      => '#',
        ];
    }
}
