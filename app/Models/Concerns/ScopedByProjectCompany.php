<?php

namespace App\Models\Concerns;

use App\Support\DemoCompany;
use Illuminate\Database\Eloquent\Builder;

/** Data milik proyek ikut tersaring sesuai perusahaan proyeknya. */
trait ScopedByProjectCompany
{
    public static function bootScopedByProjectCompany(): void
    {
        static::addGlobalScope('company', fn (Builder $q) => DemoCompany::scopeViaProject($q, static::$projectOptional ?? false));
    }
}
