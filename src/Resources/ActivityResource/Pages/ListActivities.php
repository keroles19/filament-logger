<?php

namespace Keroles\FilamentLogger\Resources\ActivityResource\Pages;

use Filament\Resources\Pages\ListRecords;
use Keroles\FilamentLogger\Resources\ActivityResource;

class ListActivities extends ListRecords
{
    public static function getResource(): string
    {
        return config('filament-logger.activity_resource', ActivityResource::class);
    }
}
