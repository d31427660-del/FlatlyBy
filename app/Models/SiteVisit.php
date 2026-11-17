<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SiteVisit extends Model
{
    protected $table = 'site_visits';

    protected $fillable = [
        'ip_address',
        'session_id',
        'device_type',
        'page_url',
        'visited_date',
    ];

    protected $casts = [
        'visited_date' => 'date',
    ];
}
