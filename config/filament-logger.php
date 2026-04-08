<?php

use Keroles\FilamentLogger\Loggers\AccessLogger;
use Keroles\FilamentLogger\Loggers\ModelLogger;
use Keroles\FilamentLogger\Loggers\NotificationLogger;
use Keroles\FilamentLogger\Loggers\ResourceLogger;
use Keroles\FilamentLogger\Resources\ActivityResource;

return [
    'datetime_format' => 'd/m/Y H:i:s',
    'date_format' => 'd/m/Y',

    /*
    |--------------------------------------------------------------------------
    | Activity Filament resource class
    |--------------------------------------------------------------------------
    |
    | Override with your own Resource subclass to customize pages, policies, etc.
    |
    */
    'activity_resource' => ActivityResource::class,

    'scoped_to_tenant' => true,
    'navigation_sort' => null,

    /*
    |--------------------------------------------------------------------------
    | Activity list / table UI
    |--------------------------------------------------------------------------
    |
    | - global_search: search box on the activity list (description, event, log_name, subject).
    | - global_search_properties_old_attributes: also match Spatie JSON "old" and "attributes" (new) blobs (keys or values as text).
    | - resolve_subject_label: show "ModelName: label" when the subject model exists and has a name-like attribute.
    | - eager_load_relations: relations to eager load on the list query (override getEloquentQuery on a custom resource if needed).
    | - bulk_delete / row_delete: show delete actions; authorization uses AuthorizesActivityDeletion (bind in your AppServiceProvider).
    | - include_spatie_default_in_type_filter: add Spatie's default log_name ("default") to type filter + badge colors when using LogsActivity alone.
    |
    */
    'table' => [
        'global_search' => true,
        'global_search_properties_old_attributes' => true,
        'resolve_subject_label' => true,
        'eager_load_relations' => ['subject', 'causer'],
        'bulk_delete' => false,
        'row_delete' => false,
        'include_spatie_default_in_type_filter' => true,
    ],

    'resources' => [
        'enabled' => true,
        'log_name' => 'Resource',
        'logger' => ResourceLogger::class,
        'color' => 'success',

        'exclude' => [
            // App\Filament\Resources\UserResource::class,
        ],
        'cluster' => null,
        'navigation_group' => 'Settings',
    ],

    'access' => [
        'enabled' => true,
        'logger' => AccessLogger::class,
        'color' => 'danger',
        'log_name' => 'Access',
    ],

    'notifications' => [
        'enabled' => true,
        'logger' => NotificationLogger::class,
        'color' => null,
        'log_name' => 'Notification',
    ],

    'models' => [
        'enabled' => true,
        'log_name' => 'Model',
        'color' => 'warning',
        'logger' => ModelLogger::class,
        'register' => [
            // App\Models\User::class,
        ],
    ],

    'custom' => [
        // [
        //     'log_name' => 'Custom',
        //     'color' => 'primary',
        // ]
    ],
];
