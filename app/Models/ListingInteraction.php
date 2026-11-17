<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ListingInteraction extends Model
{
    protected $table = 'listing_interactions';

    protected $fillable = [
        'listing_id',
        'interaction_type',
        'device_type'
    ];

    public function listing(): BelongsTo
    {
        return $this->belongsTo(Listing::class, 'listing_id');
    }
}
