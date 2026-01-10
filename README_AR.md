# مسجل الأنشطة لـ Filament 4

> [!NOTE]
> هذا fork من حزمة [Z3d0X/filament-logger](https://github.com/Z3d0X/filament-logger) الأصلية مع دعم Filament 4.
> يتم صيانته حالياً بواسطة [keroles19](https://github.com/keroles19) مع تحسينات وإصلاحات.

[![Latest Version on Packagist](https://img.shields.io/packagist/v/keroles/filament-logger.svg?style=for-the-badge)](https://packagist.org/packages/keroles/filament-logger)
[![Total Downloads](https://img.shields.io/packagist/dt/keroles/filament-logger.svg?style=for-the-badge)](https://packagist.org/packages/keroles/filament-logger)

## المميزات

يمكنك اختيار ما تريد تسجيله وكيفية تسجيله:

- ✅ تسجيل أحداث موارد Filament (إنشاء، تحديث، حذف)
- ✅ تسجيل أحداث تسجيل الدخول
- ✅ تسجيل أحداث الإشعارات
- ✅ تسجيل أحداث النماذج (Models)
- ✅ سهولة التوسع لتسجيل أحداث مخصصة
- ✅ متوافق بالكامل مع Filament 4

## المتطلبات

| المكون | الإصدار |
|--------|---------|
| PHP | ^8.2 |
| Laravel | ^11.0 \| ^12.0 |
| Filament | ^4.0 |
| Spatie Activity Log | ^4.5 |

## التثبيت

### 1. تثبيت الحزمة

تستخدم هذه الحزمة [spatie/laravel-activitylog](https://spatie.be/docs/laravel-activitylog)، يمكنك العثور على تعليمات الإعداد [هنا](https://spatie.be/docs/laravel-activitylog/v4/installation-and-setup)

```bash
composer require keroles/filament-logger
```

### 2. تشغيل أمر التثبيت

```bash
php artisan filament-logger:install
```

سيقوم هذا بنشر ملفات الإعدادات والـ migrations من `spatie/laravel-activitylog`

### 3. تسجيل الإضافة

في ملف `PanelProvider`:

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

## الاستخدام

### تسجيل أحداث الموارد (Resources)

بشكل افتراضي، يتم تسجيل جميع أحداث موارد Filament تلقائياً. لاستثناء مورد معين:

```php
// في config/filament-logger.php
'resources' => [
    'enabled' => true,
    'exclude' => [
        App\Filament\Resources\UserResource::class,
    ],
],
```

### تسجيل أحداث النماذج (Models)

لتسجيل أحداث نموذج معين:

```php
// في config/filament-logger.php
'models' => [
    'enabled' => true,
    'register' => [
        App\Models\User::class,
        App\Models\Post::class,
    ],
],
```

### تخصيص السجلات

يمكنك تخصيص كيفية عرض السجلات في ملف الإعدادات:

```php
// في config/filament-logger.php
return [
    'datetime_format' => 'd/m/Y H:i:s',
    'resources' => [
        'enabled' => true,
        'log_name' => 'Resource',
        'color' => 'success',
    ],
    'access' => [
        'enabled' => true,
        'log_name' => 'Access',
        'color' => 'danger',
    ],
];
```

## التصاريح (Authorization)

لتطبيق سياسات الصلاحيات على `ActivityResource`، قم بتسجيل `Spatie\Activitylog\Models\Activity` في `AuthServiceProvider`:

```php
use App\Policies\ActivityPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Spatie\Activitylog\Models\Activity;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        Activity::class => ActivityPolicy::class,
    ];
}
```

## التعريب

الحزمة تدعم اللغة العربية. لنشر ملفات الترجمة:

```bash
php artisan vendor:publish --tag="filament-logger-translations"
```

## ما الجديد في هذا Fork؟

### التحسينات المدمجة
- ✅ التوافق الكامل مع Filament 4
- ✅ إصلاحات للأخطاء وتحسينات في الأداء
- ✅ بنية حديثة للإضافات (Plugin architecture)
- ✅ توثيق محسّن
- ✅ صيانة نشطة

### الاعتمادات
- **المطور الأصلي**: Ziyaan Hassan (@Z3d0X)
- **المشرف الحالي**: Keroles Atef (@keroles19)

## الترخيص

رخصة MIT - نفس الحزمة الأصلية

## المساهمة

المساهمات مرحب بها! لا تتردد في إرسال Pull Request.

## الدعم

للمشاكل والأسئلة، يرجى استخدام صفحة Issues على GitHub:
https://github.com/keroles19/filament-logger/issues

---

## روابط مفيدة

- [التوثيق الكامل (إنجليزي)](./README.md)
- [معلومات Fork](./FORK_INFO.md)
- [دليل النشر](./PUBLISHING_GUIDE.md)
- [ملخص المشروع](./PROJECT_SUMMARY.md)

