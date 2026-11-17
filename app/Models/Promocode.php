<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Promocode extends Model
{
    protected $table = 'promocodes';

    protected $fillable = [
        'code', 'type', 'value', 'min_amount', 'max_uses', 'max_uses_per_user',
        'starts_at', 'expires_at', 'is_active', 'description', 'bonus_days'
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'expires_at' => 'datetime',
        'is_active' => 'boolean',
        'value' => 'double',
        'min_amount' => 'double',
    ];

    public function usages(): HasMany
    {
        return $this->hasMany(PromocodeUsage::class, 'promocode_id');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class, 'promocode_id');
    }

    /**
     * Check if the promocode is currently valid.
     */
    public function isValidForUser(User $user, float $amount = 0.00): bool
    {
        if (!$this->is_active) {
            return false;
        }

        $now = now();
        if ($this->starts_at && $this->starts_at->isFuture()) {
            return false;
        }
        if ($this->expires_at && $this->expires_at->isPast()) {
            return false;
        }

        if ($this->min_amount && $amount < $this->min_amount) {
            return false;
        }

        // Check global usage limit
        if ($this->max_uses && $this->usages()->count() >= $this->max_uses) {
            return false;
        }

        // Check user usage limit is disabled to allow unlimited activations for the same user
        /*
        if ($this->max_uses_per_user) {
            $userUsages = $this->usages()->where('user_id', $user->id)->count();
            if ($userUsages >= $this->max_uses_per_user) {
                return false;
            }
        }
        */

        return true;
    }
}
