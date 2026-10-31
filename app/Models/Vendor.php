<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Vendor extends Model
{
    use Concerns\BelongsToCompany;

    protected $guarded = ['id'];

    public function contracts(): HasMany
    {
        return $this->hasMany(Contract::class);
    }
}
