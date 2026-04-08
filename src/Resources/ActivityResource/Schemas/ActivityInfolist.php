<?php

namespace Keroles\FilamentLogger\Resources\ActivityResource\Schemas;

use Filament\Infolists\Components\KeyValueEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Model;
use Keroles\FilamentLogger\Support\ActivitySubjectLabel;
use Spatie\Activitylog\Contracts\Activity;
use Spatie\Activitylog\Models\Activity as ActivityModel;

class ActivityInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(4)
            ->components([
                Section::make()
                    ->columnSpan(3)
                    ->columns(2)
                    ->schema([
                        TextEntry::make('causer.name')
                            ->label(__('filament-logger::filament-logger.resource.label.user'))
                            ->placeholder('-'),

                        TextEntry::make('subject_type')
                            ->label(__('filament-logger::filament-logger.resource.label.subject'))
                            ->placeholder('-')
                            ->formatStateUsing(function (?Model $record, ?string $state) {
                                /** @var Activity&ActivityModel $record */
                                if (! $state || ! $record) {
                                    return '-';
                                }

                                return ActivitySubjectLabel::format(
                                    $state,
                                    $record->subject_id,
                                    $record->subject,
                                    (bool) config('filament-logger.table.resolve_subject_label', true),
                                );
                            }),

                        TextEntry::make('description')
                            ->label(__('filament-logger::filament-logger.resource.label.description'))
                            ->columnSpanFull(),
                    ]),

                Section::make()
                    ->columnSpan(1)
                    ->schema([
                        TextEntry::make('log_name')
                            ->label(__('filament-logger::filament-logger.resource.label.type'))
                            ->formatStateUsing(function (?Model $record): string {
                                /** @var Activity&ActivityModel $record */
                                return $record->log_name ? ucwords($record->log_name) : '-';
                            }),

                        TextEntry::make('event')
                            ->label(__('filament-logger::filament-logger.resource.label.event'))
                            ->formatStateUsing(function (?Model $record): string {
                                /** @phpstan-ignore-next-line */
                                return $record?->event ? ucwords($record?->event) : '-';
                            }),

                        TextEntry::make('created_at')
                            ->label(__('filament-logger::filament-logger.resource.label.logged_at'))
                            ->formatStateUsing(function (?Model $record): string {
                                /** @var Activity&ActivityModel $record */
                                return $record->created_at ? "{$record->created_at->format(config('filament-logger.datetime_format', 'd/m/Y H:i:s'))}" : '-';
                            }),
                    ]),

                Section::make('Old')
                    ->heading(__('filament-logger::filament-logger.resource.label.old'))
                    ->columns()
                    ->columnSpan(2)
                    ->visible(fn (?Model $record) => $record?->properties?->has('old') ?? false)
                    ->schema([
                        KeyValueEntry::make('properties.old')
                            ->label('')
                            ->columnSpanFull(),
                    ]),

                Section::make('New')
                    ->heading(__('filament-logger::filament-logger.resource.label.new'))
                    ->columns()
                    ->columnSpan(2)
                    ->visible(fn (?Model $record) => $record?->properties?->has('attributes') ?? false)
                    ->schema([
                        KeyValueEntry::make('properties.attributes')
                            ->label('')
                            ->columnSpanFull(),
                    ]),

                Section::make('Properties')
                    ->heading(__('filament-logger::filament-logger.resource.label.properties'))
                    ->columns()
                    ->columnSpan(4)
                    ->visible(function (?Model $record) {
                        /** @var Activity&ActivityModel $record */
                        if (! $record->properties?->count()) {
                            return false;
                        }
                        $properties = $record->properties->except(['attributes', 'old']);

                        return $properties->count() > 0;
                    })
                    ->schema([
                        KeyValueEntry::make('properties')
                            ->label('')
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
