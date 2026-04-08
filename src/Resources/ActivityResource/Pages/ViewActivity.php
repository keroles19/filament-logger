<?php

namespace Keroles\FilamentLogger\Resources\ActivityResource\Pages;

use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\ViewRecord;
use Keroles\FilamentLogger\Contracts\AuthorizesActivityDeletion;
use Keroles\FilamentLogger\Resources\ActivityResource;

class ViewActivity extends ViewRecord
{
    public static function getResource(): string
    {
        return config('filament-logger.activity_resource', ActivityResource::class);
    }

    protected function getHeaderActions(): array
    {
        if (! config('filament-logger.table.row_delete', false)) {
            return [];
        }

        return [
            DeleteAction::make()
                ->visible(fn (): bool => auth()->check() && app(AuthorizesActivityDeletion::class)->canDelete(auth()->user())),
        ];
    }
}
