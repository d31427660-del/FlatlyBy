<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    protected $table = 'payments';

    protected $fillable = [
        'user_id', 'listing_id', 'tariff_id', 'amount', 'currency', 'status',
        'payment_provider_id', 'checkout_token', 'duration_months', 'promocode_id',
        'discount_amount', 'metadata'
    ];

    protected $casts = [
        'metadata' => 'array',
        'amount' => 'double',
        'discount_amount' => 'double',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function listing(): BelongsTo
    {
        return $this->belongsTo(Listing::class, 'listing_id');
    }

    public function tariff(): BelongsTo
    {
        return $this->belongsTo(Tariff::class, 'tariff_id');
    }

    public function promocode(): BelongsTo
    {
        return $this->belongsTo(Promocode::class, 'promocode_id');
    }
}
