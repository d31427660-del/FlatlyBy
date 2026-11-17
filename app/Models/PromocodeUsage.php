<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PromocodeUsage extends Model
{
    protected $table = 'promocode_usage';

    protected $fillable = [
        'promocode_id', 'user_id', 'payment_id', 'discount_amount', 'used_at'
    ];

    protected $casts = [
        'used_at' => 'datetime',
        'discount_amount' => 'double',
    ];

    public function promocode(): BelongsTo
    {
        return $this->belongsTo(Promocode::class, 'promocode_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class, 'payment_id');
    }
}
