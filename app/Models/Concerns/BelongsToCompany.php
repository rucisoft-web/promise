<?php

namespace App\Models\Concerns;

use App\Support\DemoCompany;
use Illuminate\Database\Eloquent\Builder;

/** Model dengan kolom `company` (portofolio, proyek, sumber daya, vendor). */
trait BelongsToCompany
{
    public static function bootBelongsToCompany(): void
    {
        static::addGlobalScope('company', fn (Builder $q) => DemoCompany::scopeOwn($q));
        static::creating(function ($model) {
            if (blank($model->company)) {
                $model->company = method_exists($model, 'inheritedCompany') ? ($model->inheritedCompany() ?? DemoCompany::current()) : DemoCompany::current();
            }
        });
    }
}
