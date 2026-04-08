<?php

use Illuminate\Database\Eloquent\Model;
use Keroles\FilamentLogger\Support\ActivitySubjectLabel;

describe('ActivitySubjectLabel', function () {
    it('returns dash when subject type is empty', function () {
        expect(ActivitySubjectLabel::format(null, 1, null, true))->toBe('-');
    });

    it('formats headline and id when no label resolves', function () {
        $model = new class extends Model
        {
            protected $guarded = [];
        };
        $model->id = 5;

        $result = ActivitySubjectLabel::format('App\\Models\\SubCategory', 18, $model, true);

        expect($result)->toContain('# 18');
    });

    it('uses name when present on subject', function () {
        $model = new class extends Model
        {
            protected $guarded = [];

            public $name = 'Books';
        };

        $result = ActivitySubjectLabel::format('App\\Models\\Category', 1, $model, true);

        expect($result)->toBe('Category: Books');
    });
});
