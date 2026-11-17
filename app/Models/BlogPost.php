<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BlogPost extends Model
{
    protected $fillable = [
        'title',
        'slug',
        'meta_title',
        'meta_description',
        'summary',
        'content',
        'image',
        'source_name',
        'source_url',
        'views'
    ];

    /**
     * Get route key name for implicit binding.
     */
    public function getRouteKeyName()
    {
        return 'slug';
    }
}
