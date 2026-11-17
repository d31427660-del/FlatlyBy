<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tariff extends Model
{
    protected $table = 'tariffs';

    protected $fillable = [
        'name', 'property_type', 'price', 'duration_days'
    ];

    public function userTariffs(): HasMany
    {
        return $this->hasMany(UserTariff::class, 'tariff_id');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class, 'tariff_id');
    }
}
