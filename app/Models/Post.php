<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class Post extends Model implements HasMedia
{
    use HasFactory;
    use InteractsWithMedia;
    use HasSlug;

    protected $fillable = [
        // 'image',
        'title',
        'slug',
        'content',
        'category_id',
        'user_id',
        'published_at',
    ];

    protected $casts = [
        'published_at' => 'datetime',
    ];

    public function registerMediaConversions(?Media $media = null): void
    {
        $this
            ->addMediaConversion('preview')
            ->width(400);

        $this
            ->addMediaConversion('large')
            ->width(1200);
    }
    
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('default')
            ->singleFile()
            ->useDisk('public');
    }

    /**
     * Get the options for generating the slug.
     */
    public function getSlugOptions() : SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('title')
            ->saveSlugsTo('slug');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function claps()
    {
        return $this->hasMany(Clap::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class)->latest();
    }

    public function readTime($wordsPerMinute = 100)
    {
        $wordCount = str_word_count(strip_tags($this->content));
        $minutes = ceil($wordCount / $wordsPerMinute);

        return max(1, $minutes);
    }
    
    public function imageUrl($conversionName = '')
    {
        $media = $this->getFirstMedia();
        if (!$media) {
            return null;
        }
        
        try {
            // Get URL from media library
            $url = null;
            if ($conversionName && $media->hasGeneratedConversion($conversionName)) {
                $url = $media->getUrl($conversionName);
            } else {
                $url = $media->getUrl();
            }
            
            // Ensure URL starts with /storage/ for public disk
            if ($url && $media->disk === 'public') {
                // If URL doesn't start with /storage/, fix it
                if (!str_starts_with($url, '/storage/') && !str_starts_with($url, 'http')) {
                    // Extract the path after storage/app/public
                    $path = $media->getPath();
                    $relativePath = str_replace(storage_path('app/public'), '', $path);
                    $relativePath = ltrim(str_replace('\\', '/', $relativePath), '/');
                    $url = '/storage/' . $relativePath;
                }
            }
            
            return $url;
        } catch (\Exception $e) {
            \Log::warning('Failed to get media URL: ' . $e->getMessage());
            return null;
        }
    }
}
