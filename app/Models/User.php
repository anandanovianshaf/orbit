<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class User extends Authenticatable implements MustVerifyEmail, HasMedia
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, InteractsWithMedia;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'username',
        'image',
        'bio',
        'email',
        'password',
    ];

    /**
     * The attributses that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }


    public function registerMediaConversions(?Media $media = null): void
    {
        $this
            ->addMediaConversion('avatar')
            ->width(128)
            ->crop(128, 128);
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('avatar')
            ->singleFile()
            ->useDisk('public');
    }

    public function posts()
    {
        return $this->hasMany(Post::class);
    }

    public function following()
    {
        return $this->belongsToMany(User::class, 'followers', 'follower_id', 'user_id');
    }

    public function followers()
    {
        return $this->belongsToMany(User::class, 'followers', 'user_id', 'follower_id');
    }

    public function imageUrl()
    {
        $media = $this->getFirstMedia('avatar');
        if (!$media) {
            return null;
        }
        
        try {
            // Get URL from media library
            $url = null;
            if ($media->hasGeneratedConversion('avatar')) {
                $url = $media->getUrl('avatar');
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

    public function isFollowedBy(?User $user)
    {
        if (!$user) {
            return false;
        }
        return $this->followers()->where('follower_id', $user->id)->exists();
    }

    public function hasClapped(Post $post)
    {
        return $post->claps()->where('user_id', $this->id)->exists();
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }
}
