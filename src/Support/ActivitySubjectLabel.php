<?php

namespace Keroles\FilamentLogger\Support;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ActivitySubjectLabel
{
    public static function format(
        ?string $subjectType,
        int|string|null $subjectId,
        ?Model $subject,
        bool $resolveLabel,
    ): string {
        if (! $subjectType) {
            return '-';
        }

        $headline = Str::of($subjectType)->afterLast('\\')->headline()->toString();

        if ($resolveLabel && $subject) {
            $label = self::resolveModelLabel($subject);
            if ($label !== null && $label !== '') {
                return "{$headline}: {$label}";
            }
        }

        return $subjectId !== null && $subjectId !== '' ? "{$headline} # {$subjectId}" : $headline;
    }

    public static function resolveModelLabel(Model $model): ?string
    {
        if (method_exists($model, 'getName')) {
            $name = $model->getName();
            if (filled($name)) {
                return (string) $name;
            }
        }

        foreach (['name', 'title', 'email', 'slug'] as $attribute) {
            if (isset($model->{$attribute}) && filled($model->{$attribute})) {
                return (string) $model->{$attribute};
            }
        }

        return null;
    }
}
