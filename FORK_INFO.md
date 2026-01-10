# Fork Information

## About This Fork

This is a fork of the popular `filament-logger` package, now maintained as `keroles/filament-logger`.

### Package History

1. **Original Package**: [Z3d0X/filament-logger](https://github.com/Z3d0X/filament-logger)
   - Created by Ziyaan Hassan (@Z3d0X)
   - Supported Filament 2 & 3
   - No longer maintained

2. **Current Fork**: [keroles19/filament-logger](https://github.com/keroles19/filament-logger)
   - Maintained by Keroles Atef (@keroles19)
   - Filament 4 support
   - Includes bug fixes and improvements
   - Continues active development

### Key Changes in This Fork

#### Improvements
- ✅ Full Filament 4 compatibility
- ✅ Bug fixes and performance improvements
- ✅ Modern plugin architecture
- ✅ Enhanced documentation
- ✅ Active maintenance

#### Package Updates
- Package name: `keroles/filament-logger`
- Namespace: `Keroles\FilamentLogger`
- Repository: https://github.com/keroles19/filament-logger

### Installation

```bash
composer require keroles/filament-logger
php artisan filament-logger:install
```

### Configuration

Register the plugin in your PanelProvider:

```php
use Keroles\FilamentLogger\FilamentLoggerPlugin;

public function panel(Panel $panel): Panel
{
    return $panel
        ->plugins([
            FilamentLoggerPlugin::make(),
        ]);
}
```

### Requirements

- PHP ^8.2
- Laravel ^11.0 | ^12.0
- Filament ^4.0
- spatie/laravel-activitylog ^4.5

### Credits

- **Original Developer**: Ziyaan Hassan (@Z3d0X)
- **Current Maintainer**: Keroles Atef (@keroles19)

### License

MIT License - Same as the original package

### Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

### Support

For issues and questions, please use the GitHub Issues page:
https://github.com/keroles19/filament-logger/issues

