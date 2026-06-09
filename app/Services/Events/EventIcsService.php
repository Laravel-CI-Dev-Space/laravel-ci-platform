<?php

namespace App\Services\Events;

use App\Models\Event;
use App\Models\EventIcsExport;
use App\Models\User;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;

class EventIcsService
{
    public function downloadResponse(Event $event, User $user): StreamedResponse
    {
        $content  = $this->build($event);
        $filename = Str::slug($event->title) . '.ics';

        EventIcsExport::create([
            'event_id'  => $event->id,
            'user_id'   => $user->id,
            'file_path' => $filename,
        ]);

        return response()->streamDownload(
            fn () => print ($content),
            $filename,
            ['Content-Type' => 'text/calendar; charset=utf-8'],
        );
    }

    public function build(Event $event): string
    {
        $uid   = "event-{$event->id}@laravel-ci-platform";
        $start = $event->start_date->utc()->format('Ymd\THis\Z');
        $end   = $event->end_date->utc()->format('Ymd\THis\Z');
        $stamp = now()->utc()->format('Ymd\THis\Z');
        $title = $this->escape($event->title);
        $desc  = $this->escape(strip_tags($event->description));
        $loc   = $this->escape($event->location ?? $event->meeting_link ?? 'En ligne');

        return implode("\r\n", [
            'BEGIN:VCALENDAR',
            'VERSION:2.0',
            'PRODID:-//Laravel CI//Events//FR',
            'CALSCALE:GREGORIAN',
            'METHOD:PUBLISH',
            'BEGIN:VEVENT',
            "UID:{$uid}",
            "DTSTAMP:{$stamp}",
            "DTSTART:{$start}",
            "DTEND:{$end}",
            "SUMMARY:{$title}",
            "DESCRIPTION:{$desc}",
            "LOCATION:{$loc}",
            'END:VEVENT',
            'END:VCALENDAR',
        ]) . "\r\n";
    }

    private function escape(string $value): string
    {
        return str_replace(
            ['\\', ';', ',', "\n", "\r"],
            ['\\\\', '\;', '\,', '\n', ''],
            $value,
        );
    }
}
