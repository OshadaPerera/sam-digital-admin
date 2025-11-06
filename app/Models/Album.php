<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Album extends Model
{
    protected $fillable = [
        'id',
        'title',
        'description',
        'cover_image',
        'type',
        'status',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get all images for this album
     */
    public function images(): HasMany
    {
        return $this->hasMany(AlbumImage::class)->orderBy('order');
    }

    /**
     * Scope a query to only include albums of a given type
     */
    public function scopeOfType($query, string $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope a query to only include active albums
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
}
