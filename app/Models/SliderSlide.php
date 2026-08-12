<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class SliderSlide extends Model
{
    protected $fillable = [
        'id',
        'type',
        'media_path',
        'alt_text',
        'screen_size',
        'order',
        'status',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected $appends = [
        'media_url',
    ];

    /**
     * Scope a query to only include slides of a given screen size
     */
    public function scopeOfScreenSize($query, string $screenSize)
    {
        return $query->where('screen_size', $screenSize);
    }

    /**
     * Scope a query to only include active slides
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope a query to order slides by order field
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('order');
    }

    /**
     * Get media URL attribute
     */
    public function getMediaUrlAttribute(): string
    {
        return $this->media_path ? Storage::url($this->media_path) : '';
    }

    /**
     * Get formatted date attribute
     */
    public function getFormattedDateAttribute(): string
    {
        return $this->created_at ? $this->created_at->format('F j, Y') : '';
    }
}
