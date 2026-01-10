# Filament Logger - Development Guide

## 📦 Package Overview

**Package Name**: `kerodev/filament-logger`  
**Version**: 1.0.1 (Filament 4)  
**Repository**: https://github.com/keroles19/filament-logger  
**Packagist**: https://packagist.org/packages/kerodev/filament-logger  

### Description
Activity logger for Filament 4 applications. Tracks and displays user activities including:
- Resource events (create, update, delete)
- Login/Access events
- Notification events
- Model changes
- Custom events

Built on top of `spatie/laravel-activitylog` with Filament 4 integration.

---

## 🎯 What Was Done

### 1. Fork & Setup
- Forked from original `Z3d0X/filament-logger` (Filament 3)
- Integrated Filament 4 updates
- Applied bug fixes for old/new attributes display

### 2. Package Rebranding
- Changed namespace: `Z3d0X\FilamentLogger` → `Keroles\FilamentLogger`
- Updated package name: `kerodev/filament-logger`
- Updated all configuration files

### 3. Filament 4 Compatibility
✅ Updated to Filament 4 APIs
✅ Uses new Plugin architecture
✅ Uses Schemas instead of old Form/Infolist builders
✅ Compatible with PHP 8.2+ and Laravel 11+

### 4. Key Improvements
- Fixed old/new attributes display for spatie model events
- Modern Filament 4 plugin registration
- Proper tenant scoping support
- Cluster support

---

## 🏗️ Package Structure

```
src/
├── FilamentLoggerPlugin.php           # Main plugin class
├── FilamentLoggerServiceProvider.php  # Service provider
├── Loggers/
│   ├── AbstractModelLogger.php        # Base logger class
│   ├── AccessLogger.php               # Login events
│   ├── ModelLogger.php                # Model changes
│   ├── NotificationLogger.php         # Notification events
│   └── ResourceLogger.php             # Filament resource events
└── Resources/
    ├── ActivityResource.php           # Main resource
    ├── Pages/
    │   ├── ListActivities.php         # List page
    │   └── ViewActivity.php           # Detail page
    ├── Schemas/
    │   └── ActivityInfolist.php       # Detail view schema
    └── Tables/
        └── ActivitiesTable.php        # Table definition

config/
└── filament-logger.php                # Configuration

resources/lang/                        # 18 translations
```

---

## 🚀 Installation & Usage

### Installation

```bash
composer require kerodev/filament-logger
php artisan filament-logger:install
```

### Basic Setup

```php
// In your PanelProvider
use Keroles\FilamentLogger\FilamentLoggerPlugin;

public function panel(Panel $panel): Panel
{
    return $panel
        ->plugins([
            FilamentLoggerPlugin::make(),
        ]);
}
```

### Configuration

Publish and edit config:
```bash
php artisan vendor:publish --tag="filament-logger-config"
```

Key configurations in `config/filament-logger.php`:
```php
return [
    'datetime_format' => 'd/m/Y H:i:s',
    
    'resources' => [
        'enabled' => true,
        'exclude' => [
            // Resources to exclude from logging
        ],
    ],
    
    'models' => [
        'enabled' => true,
        'register' => [
            // Models to log (non-resources)
            App\Models\User::class,
        ],
    ],
];
```

---

## 🛠️ Development with Cursor

### Local Development Setup

1. **Clone the package:**
```bash
cd /your-project-path
git clone https://github.com/keroles19/filament-logger.git
```

2. **Install in Laravel project for testing:**

Add to `composer.json`:
```json
{
    "repositories": [
        {
            "type": "path",
            "url": "../filament-logger"
        }
    ],
    "require": {
        "kerodev/filament-logger": "*"
    },
    "minimum-stability": "dev"
}
```

Then run:
```bash
composer update kerodev/filament-logger
```

3. **Development workflow:**
- Make changes in the cloned package directory
- Test in your Laravel project
- Commit and push to GitHub
- Packagist auto-updates (if webhook configured)

---

## 📝 Key Files to Customize

### 1. ActivityResource.php
Main resource file - controls navigation, icons, policies

```php
namespace Keroles\FilamentLogger\Resources;

class ActivityResource extends Resource
{
    protected static ?string $navigationIcon = Heroicon::OutlinedClipboardDocumentList;
    
    public static function getNavigationGroup(): ?string
    {
        return __(config('filament-logger.resources.navigation_group'));
    }
    
    // Customize table, infolist, pages here
}
```

### 2. ActivitiesTable.php
Table structure - columns, filters, actions

```php
public static function configure(Table $table): Table
{
    return $table
        ->columns([
            // Add/modify columns
        ])
        ->filters([
            // Add/modify filters
        ]);
}
```

### 3. ActivityInfolist.php
Detail view layout - how activity details are displayed

```php
public static function configure(Schema $schema): Schema
{
    return $schema
        ->columns(4)
        ->components([
            // Customize detail view
        ]);
}
```

### 4. Loggers
Custom logging behavior for different event types

**ResourceLogger.php**: Logs Filament resource events
**AccessLogger.php**: Logs login events
**ModelLogger.php**: Logs model changes
**NotificationLogger.php**: Logs notifications

---

## 🎨 Customization Examples

### Add Custom Logger

```php
// app/Loggers/CustomLogger.php
namespace App\Loggers;

use Keroles\FilamentLogger\Loggers\AbstractModelLogger;

class CustomLogger extends AbstractModelLogger
{
    public function handle(Model $model, string $event): void
    {
        activity()
            ->performedOn($model)
            ->event($event)
            ->log('Custom event logged');
    }
}
```

Register in config:
```php
'custom' => [
    [
        'log_name' => 'Custom',
        'color' => 'primary',
        'logger' => App\Loggers\CustomLogger::class,
    ]
],
```

### Customize Table Columns

In `ActivitiesTable.php`:
```php
TextColumn::make('custom_field')
    ->label('Custom Field')
    ->searchable()
    ->sortable(),
```

### Add Custom Filters

```php
SelectFilter::make('log_name')
    ->label('Type')
    ->options([
        'Resource' => 'Resource',
        'Access' => 'Access',
        'Custom' => 'Custom',
    ]),
```

---

## 🔧 Common Development Tasks

### 1. Update Package Locally
```bash
cd /path/to/filament-logger
git pull origin main
composer update
```

### 2. Test Changes
```bash
# In your Laravel project
composer update kerodev/filament-logger
php artisan optimize:clear
```

### 3. Publish Updates
```bash
git add .
git commit -m "feat: description of changes"
git push origin main
```

### 4. Create Release
```bash
git tag -a v1.0.2 -m "Release version 1.0.2"
git push origin v1.0.2
```

---

## 📚 Important Filament 4 Concepts

### Schemas
Filament 4 uses "Schemas" for forms and infolists:
```php
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;

Schema::make()
    ->components([
        Section::make()->schema([...])
    ]);
```

### Plugin Architecture
Register resources through plugin:
```php
class FilamentLoggerPlugin implements Plugin
{
    public function register(Panel $panel): void
    {
        $panel->resources([
            ActivityResource::class,
        ]);
    }
}
```

### Resources Structure
```php
class ActivityResource extends Resource
{
    public static function table(Table $table): Table { }
    public static function infolist(Schema $schema): Schema { }
    public static function getPages(): array { }
}
```

---

## 🐛 Debugging Tips

### Enable Query Logging
```php
// In a service provider
\DB::listen(function($query) {
    \Log::info($query->sql, $query->bindings);
});
```

### Check Activity Logs
```php
// In Tinker
\Spatie\Activitylog\Models\Activity::latest()->take(10)->get();
```

### Clear Filament Cache
```bash
php artisan filament:cache-components
php artisan optimize:clear
```

---

## 📦 Dependencies

```json
{
    "php": "^8.2",
    "filament/filament": "^4.0",
    "illuminate/contracts": "^11.0 | ^12.0",
    "spatie/laravel-activitylog": "^4.5",
    "spatie/laravel-package-tools": "^1.13.5"
}
```

---

## 🎯 Future Enhancement Ideas

### Features to Add
- [ ] Export activities to CSV/Excel
- [ ] Advanced filtering by date range
- [ ] Activity statistics dashboard widget
- [ ] Bulk delete old activities
- [ ] Email notifications for specific events
- [ ] Activity timeline view
- [ ] Restore deleted records from activity log
- [ ] Compare changes between versions

### UI Improvements
- [ ] Dark mode optimizations
- [ ] Mobile responsive improvements
- [ ] Activity icons based on event type
- [ ] Color coding for different log types

### Performance
- [ ] Pagination optimization for large datasets
- [ ] Caching for frequently accessed activities
- [ ] Archive old activities automatically

---

## 🔗 Useful Links

- **Filament 4 Docs**: https://filamentphp.com/docs/4.x
- **Spatie Activity Log**: https://spatie.be/docs/laravel-activitylog/v4
- **Original Package**: https://github.com/Z3d0X/filament-logger
- **This Package**: https://github.com/keroles19/filament-logger
- **Packagist**: https://packagist.org/packages/kerodev/filament-logger

---

## 💡 Tips for Cursor Development

### Use Cursor Rules
Create `.cursorrules` in project root:
```
This is a Filament 4 package for logging activities.

Key points:
- Use Filament 4 APIs (Schemas, not Forms)
- Follow PSR-12 coding standards
- All changes should be Filament 4 compatible
- Test with PHP 8.2+ and Laravel 11+
```

### Quick Commands
```bash
# Search for usage
grep -r "ActivityResource" src/

# Find all loggers
find src/Loggers -name "*.php"

# Check namespace usage
grep -r "namespace Keroles" src/
```

### AI Assistant Prompts
- "Update this to use Filament 4 Schema instead of Form"
- "Add a new column to the activities table"
- "Create a custom logger for payment events"
- "Optimize the table query for large datasets"

---

## 📄 License

MIT License - Same as original package

---

## ✅ Package Status

✅ Published on Packagist  
✅ Filament 4 Compatible  
✅ PHP 8.2+ Compatible  
✅ Laravel 11+ Compatible  
✅ All translations included  
✅ Documentation complete  
✅ Ready for production use  

---

**Last Updated**: January 10, 2026  
**Maintained by**: Keroles Atef (@keroles19)  
**Original Creator**: Ziyaan Hassan (@Z3d0X)

