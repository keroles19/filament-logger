<?php

namespace Keroles\FilamentLogger\Resources\ActivityResource\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ViewAction;
use Filament\Facades\Filament;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Keroles\FilamentLogger\Contracts\AuthorizesActivityDeletion;
use Keroles\FilamentLogger\Support\ActivitySubjectLabel;
use Spatie\Activitylog\Contracts\Activity;
use Spatie\Activitylog\Models\Activity as ActivityModel;

class ActivitiesTable
{
    public static function configure(Table $table): Table
    {
        $resolveSubject = (bool) config('filament-logger.table.resolve_subject_label', true);

        $table = $table
            ->columns([
                TextColumn::make('log_name')
                    ->label(__('filament-logger::filament-logger.resource.label.type'))
                    ->badge()
                    ->colors(static::getLogNameColors())
                    ->formatStateUsing(fn ($state) => ucwords((string) $state))
                    ->sortable(),

                TextColumn::make('event')
                    ->label(__('filament-logger::filament-logger.resource.label.event'))
                    ->sortable(),

                TextColumn::make('description')
                    ->label(__('filament-logger::filament-logger.resource.label.description'))
                    ->toggleable()
                    ->toggledHiddenByDefault()
                    ->wrap(),

                TextColumn::make('subject_type')
                    ->label(__('filament-logger::filament-logger.resource.label.subject'))
                    ->formatStateUsing(function ($state, Model $record) use ($resolveSubject) {
                        /** @var Activity&ActivityModel $record */
                        if (! $state) {
                            return '-';
                        }

                        return ActivitySubjectLabel::format(
                            $state,
                            $record->subject_id,
                            $record->subject,
                            $resolveSubject,
                        );
                    }),

                TextColumn::make('causer.name')
                    ->label(__('filament-logger::filament-logger.resource.label.user')),

                TextColumn::make('created_at')
                    ->label(__('filament-logger::filament-logger.resource.label.logged_at'))
                    ->dateTime(config('filament-logger.datetime_format', 'd/m/Y H:i:s'), config('app.timezone'))
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('log_name')
                    ->label(__('filament-logger::filament-logger.resource.label.type'))
                    ->options(static::getLogNameList()),

                SelectFilter::make('subject_type')
                    ->label(__('filament-logger::filament-logger.resource.label.subject_type'))
                    ->options(static::getSubjectTypeList()),

                SelectFilter::make('causer_id')
                    ->label(__('filament-logger::filament-logger.resource.label.user'))
                    ->options(function () {
                        return static::getUsersList();
                    })
                    ->searchable()
                    ->multiple(),

                Filter::make('properties->old')
                    ->indicateUsing(function (array $data): ?string {
                        if (! $data['old']) {
                            return null;
                        }

                        return __('filament-logger::filament-logger.resource.label.old_attributes').$data['old'];
                    })
                    ->schema([
                        TextInput::make('old')
                            ->label(__('filament-logger::filament-logger.resource.label.old'))
                            ->hint(__('filament-logger::filament-logger.resource.label.properties_hint')),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        if (! $data['old']) {
                            return $query;
                        }

                        return $query->where('properties->old', 'like', "%{$data['old']}%");
                    }),

                Filter::make('properties->attributes')
                    ->indicateUsing(function (array $data): ?string {
                        if (! $data['new']) {
                            return null;
                        }

                        return __('filament-logger::filament-logger.resource.label.new_attributes').$data['new'];
                    })
                    ->schema([
                        TextInput::make('new')
                            ->label(__('filament-logger::filament-logger.resource.label.new'))
                            ->hint(__('filament-logger::filament-logger.resource.label.properties_hint')),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        if (! $data['new']) {
                            return $query;
                        }

                        return $query->where('properties->attributes', 'like', "%{$data['new']}%");
                    }),

                Filter::make('created_at')
                    ->schema([
                        DatePicker::make('logged_at')
                            ->label(__('filament-logger::filament-logger.resource.label.logged_at'))
                            ->displayFormat(config('filament-logger.date_format', 'd/m/Y')),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['logged_at'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', $date),
                            );
                    }),
            ]);

        if (config('filament-logger.table.global_search', true)) {
            $table = $table
                ->searchable()
                ->searchUsing(function (Builder $query, string $search): Builder {
                    return $query->where(function (Builder $q) use ($search) {
                        $term = '%'.$search.'%';
                        $q->where('description', 'like', $term)
                            ->orWhere('event', 'like', $term)
                            ->orWhere('log_name', 'like', $term)
                            ->orWhere('subject_type', 'like', $term);

                        if (config('filament-logger.table.global_search_properties_old_attributes', true)) {
                            // Same JSON paths as the "old" / "new" filters: LIKE matches key names and values in the serialized JSON.
                            $q->orWhere('properties->old', 'like', $term)
                                ->orWhere('properties->attributes', 'like', $term);
                        }

                        $trimmed = trim($search);
                        if ($trimmed !== '' && ctype_digit($trimmed)) {
                            $q->orWhere('subject_id', (int) $trimmed);
                        }
                    });
                });
        }

        $toolbarActions = [];
        if (config('filament-logger.table.bulk_delete', false)) {
            $toolbarActions[] = BulkActionGroup::make([
                DeleteBulkAction::make()
                    ->visible(fn (): bool => static::userCanDeleteActivity()),
            ]);
        }

        $table = $table->toolbarActions($toolbarActions);

        $recordActions = [
            ViewAction::make(),
        ];

        if (config('filament-logger.table.row_delete', false)) {
            $recordActions[] = DeleteAction::make()
                ->visible(fn (): bool => static::userCanDeleteActivity());
        }

        $table = $table->recordActions($recordActions);

        return $table;
    }

    protected static function userCanDeleteActivity(): bool
    {
        if (! auth()->check()) {
            return false;
        }

        return app(AuthorizesActivityDeletion::class)->canDelete(auth()->user());
    }

    protected static function getSubjectTypeList(): array
    {
        if (config('filament-logger.resources.enabled', true)) {
            $subjects = [];
            $exceptResources = [...config('filament-logger.resources.exclude'), config('filament-logger.activity_resource')];
            $removedExcludedResources = collect(Filament::getResources())->filter(function ($resource) use ($exceptResources) {
                return ! in_array($resource, $exceptResources);
            });
            foreach ($removedExcludedResources as $resource) {
                $model = $resource::getModel();
                $subjects[$model] = Str::of(class_basename($model))->headline();
            }

            return $subjects;
        }

        return [];
    }

    protected static function getLogNameList(): array
    {
        $customs = [];

        foreach (config('filament-logger.custom') ?? [] as $custom) {
            $customs[$custom['log_name']] = $custom['log_name'];
        }

        $list = array_merge(
            config('filament-logger.resources.enabled') ? [
                config('filament-logger.resources.log_name') => config('filament-logger.resources.log_name'),
            ] : [],
            config('filament-logger.models.enabled') ? [
                config('filament-logger.models.log_name') => config('filament-logger.models.log_name'),
            ] : [],
            config('filament-logger.access.enabled')
                ? [config('filament-logger.access.log_name') => config('filament-logger.access.log_name')]
                : [],
            config('filament-logger.notifications.enabled') ? [
                config('filament-logger.notifications.log_name') => config('filament-logger.notifications.log_name'),
            ] : [],
            $customs,
        );

        if (config('filament-logger.table.include_spatie_default_in_type_filter', true)) {
            $list['default'] = 'default';
        }

        return $list;
    }

    protected static function getLogNameColors(): array
    {
        $customs = [];

        foreach (config('filament-logger.custom') ?? [] as $custom) {
            if (filled($custom['color'] ?? null)) {
                $customs[$custom['color']] = $custom['log_name'];
            }
        }

        $colors = array_merge(
            (config('filament-logger.resources.enabled') && config('filament-logger.resources.color')) ? [
                config('filament-logger.resources.color') => config('filament-logger.resources.log_name'),
            ] : [],
            (config('filament-logger.models.enabled') && config('filament-logger.models.color')) ? [
                config('filament-logger.models.color') => config('filament-logger.models.log_name'),
            ] : [],
            (config('filament-logger.access.enabled') && config('filament-logger.access.color')) ? [
                config('filament-logger.access.color') => config('filament-logger.access.log_name'),
            ] : [],
            (config('filament-logger.notifications.enabled') && config('filament-logger.notifications.color')) ? [
                config('filament-logger.notifications.color') => config('filament-logger.notifications.log_name'),
            ] : [],
            $customs,
        );

        if (config('filament-logger.table.include_spatie_default_in_type_filter', true)) {
            $colors['gray'] = 'default';
        }

        return $colors;
    }

    protected static function getUsersList(): array
    {
        $activityModel = config('activitylog.activity_model');

        // Get distinct causer types and IDs from activity log
        $causers = $activityModel::query()
            ->select('causer_type', 'causer_id')
            ->whereNotNull('causer_type')
            ->whereNotNull('causer_id')
            ->distinct()
            ->get()
            ->groupBy('causer_type');

        $users = [];

        foreach ($causers as $causerType => $activities) {
            if (! class_exists($causerType)) {
                continue;
            }

            $causerIds = $activities->pluck('causer_id')->unique()->toArray();
            $models = $causerType::whereIn('id', $causerIds)->get();

            foreach ($models as $model) {
                // Use getName() method if available, otherwise try common name attributes
                $name = method_exists($model, 'getName')
                    ? $model->getName()
                    : ($model->name ?? $model->email ?? $model->username ?? "User #{$model->id}");

                $users[$model->id] = $name;
            }
        }

        return $users;
    }
}
