<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class News extends Model
{
    protected $fillable = ['caption', 'category_id', 'media', 'video_link'];

    // Cast media to array
    protected $casts = [
        'media' => 'array',
    ];

    // Return full URLs for media
    protected $appends = ['media_urls'];

    public function getMediaUrlsAttribute(): array
    {
        $paths = $this->media ?? [];
        return array_map(fn($p) => asset('storage/' . $p), $paths);
    }
        public function category()
    {
        return $this->belongsTo(Category::class);
    }

}
