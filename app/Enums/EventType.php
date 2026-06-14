<?php

declare(strict_types=1);

namespace App\Enums;

use App\Enums\Concerns\HasOptions;

enum EventType: string
{
    use HasOptions;

    case Meetup     = 'meetup';
    case Webinar    = 'webinar';
    case Hackathon  = 'hackathon';
    case Conference = 'conference';
    case Workshop   = 'workshop';

    public function label(): string
    {
        return match ($this) {
            self::Meetup     => 'Meetup',
            self::Webinar    => 'Webinaire',
            self::Hackathon  => 'Hackathon',
            self::Conference => 'Conférence',
            self::Workshop   => 'Workshop',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::Meetup     => 'fa-solid fa-people-roof',
            self::Webinar    => 'fa-solid fa-video',
            self::Hackathon  => 'fa-solid fa-laptop-code',
            self::Conference => 'fa-solid fa-microphone',
            self::Workshop   => 'fa-solid fa-screwdriver-wrench',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Meetup     => '#ea580c',
            self::Webinar    => '#4361ee',
            self::Hackathon  => '#7209b7',
            self::Conference => '#2d9b4e',
            self::Workshop   => '#0891b2',
        };
    }

    public function background(): string
    {
        return match ($this) {
            self::Meetup     => '#fff4e5',
            self::Webinar    => '#e7ebff',
            self::Hackathon  => '#f1e7ff',
            self::Conference => '#e7fff1',
            self::Workshop   => '#e7f7ff',
        };
    }

    public function bannerClass(): string
    {
        return match ($this) {
            self::Meetup, self::Conference => 'ev-meetup',
            self::Webinar, self::Workshop  => 'ev-webinar',
            self::Hackathon                => 'ev-hackathon',
        };
    }
}
