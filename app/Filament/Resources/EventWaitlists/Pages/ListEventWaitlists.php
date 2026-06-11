<?php

namespace App\Filament\Resources\EventWaitlists\Pages;

use App\Filament\Resources\EventWaitlists\EventWaitlistResource;
use Filament\Resources\Pages\ListRecords;

class ListEventWaitlists extends ListRecords
{
    protected static string $resource = EventWaitlistResource::class;
}
