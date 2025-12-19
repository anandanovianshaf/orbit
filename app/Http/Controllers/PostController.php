<?php

namespace App\Http\Controllers;

use App\Http\Requests\PostCreateRequest;
use App\Http\Requests\PostUpdateRequest;
use App\Models\Post;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Carbon\Carbon;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $query =  Post::with(['user', 'media'])
            ->where('published_at', '<=', now())
            ->withCount('claps')
            ->withCount('comments')
            ->latest();

        $posts = $query->paginate(10)->withQueryString();
        return view('post.index', [
            'posts' => $posts,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = Category::get();
        
        return view('post.create', [
            'categories' => $categories,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(PostCreateRequest $request)
    {
        $data = $request->validated();

        $data['user_id'] = Auth::id();
        
        // Convert datetime-local string to Carbon instance
        // datetime-local format: "YYYY-MM-DDTHH:mm" (no timezone info)
        // datetime-local sends time in user's local timezone
        if (isset($data['published_at']) && $data['published_at']) {
            try {
                // Parse datetime-local - Carbon will interpret it in the default timezone (UTC)
                $publishedAt = Carbon::createFromFormat('Y-m-d\TH:i', $data['published_at'], config('app.timezone'));
                
                // Ensure it's in UTC timezone for database storage
                $publishedAt->setTimezone(config('app.timezone'));
                
                // Compare with now() in the same timezone
                // If published_at is greater than now(), set to now() so post appears immediately
                // This ensures posts always appear when created, regardless of timestamp
                if ($publishedAt->gt(now())) {
                    $data['published_at'] = now();
                } else {
                    $data['published_at'] = $publishedAt;
                }
            } catch (\Exception $e) {
                // If parsing fails, default to now()
                \Log::warning('Failed to parse published_at: ' . $e->getMessage());
                $data['published_at'] = now();
            }
        } else {
            $data['published_at'] = now();
        }

        // Remove image from data array as it's handled separately
        unset($data['image']);

        $post = Post::create($data);

        // Handle image upload
        if ($request->hasFile('image')) {
            try {
                // With ->nonQueued() in registerMediaConversions(), conversions are created synchronously
                $post->addMediaFromRequest('image')
                    ->usingName($data['title'] ?? 'post-image')
                    ->usingFileName($request->file('image')->getClientOriginalName())
                    ->toMediaCollection('default');
            } catch (\Exception $e) {
                // Log error and redirect back with error message
                \Log::error('Failed to upload post image: ' . $e->getMessage());
                \Log::error('Stack trace: ' . $e->getTraceAsString());
                return redirect()->back()
                    ->withInput()
                    ->withErrors(['image' => 'Failed to upload image: ' . $e->getMessage()]);
            }
        }

        return redirect()->route('dashboard');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $username, Post $post)
    {
        $post->load(['comments.user', 'user', 'category', 'media']);

        // Increment views once per session per post
        $sessionKey = 'viewed_post_' . $post->id;
        if (!request()->session()->has($sessionKey)) {
            $post->increment('views');
            request()->session()->put($sessionKey, true);
        }
        
        return view('post.show', [
            'post' => $post,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Post $post)
    {
        if ($post->user_id !== Auth::id()) {
            abort(403);
        }
        $categories = Category::get();
        return view('post.edit', [
            'post' => $post,
            'categories' => $categories,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(PostUpdateRequest $request, Post $post)
    {
        if ($post->user_id !== Auth::id()) {
            abort(403);
        }
        $data = $request->validated();
        
        // Convert datetime-local string to Carbon instance
        // datetime-local format: "YYYY-MM-DDTHH:mm" (no timezone info)
        // datetime-local sends time in user's local timezone
        if (isset($data['published_at']) && $data['published_at']) {
            try {
                // Parse datetime-local - Carbon will interpret it in the default timezone (UTC)
                $publishedAt = Carbon::createFromFormat('Y-m-d\TH:i', $data['published_at'], config('app.timezone'));
                
                // Ensure it's in UTC timezone for database storage
                $publishedAt->setTimezone(config('app.timezone'));
                
                // Compare with now() in the same timezone
                // If published_at is greater than now(), set to now() so post appears immediately
                // This ensures posts always appear when created, regardless of timestamp
                if ($publishedAt->gt(now())) {
                    $data['published_at'] = now();
                } else {
                    $data['published_at'] = $publishedAt;
                }
            } catch (\Exception $e) {
                // If parsing fails, keep existing published_at or default to now()
                \Log::warning('Failed to parse published_at: ' . $e->getMessage());
                $data['published_at'] = $post->published_at ?? now();
            }
        } else {
            $data['published_at'] = $post->published_at ?? now();
        }

        // Remove image from data array as it's handled separately
        $hasImage = isset($data['image']);
        unset($data['image']);

        $post->update($data);

        // Handle image upload
        if ($hasImage && $request->hasFile('image')) {
            try {
                // Clear existing media first (since it's singleFile collection)
                $post->clearMediaCollection('default');
                // With ->nonQueued() in registerMediaConversions(), conversions are created synchronously
                $post->addMediaFromRequest('image')
                    ->usingName($data['title'] ?? 'post-image')
                    ->usingFileName($request->file('image')->getClientOriginalName())
                    ->toMediaCollection('default');
            } catch (\Exception $e) {
                // Log error and redirect back with error message
                \Log::error('Failed to upload post image: ' . $e->getMessage());
                \Log::error('Stack trace: ' . $e->getTraceAsString());
                return redirect()->back()
                    ->withInput()
                    ->withErrors(['image' => 'Failed to upload image: ' . $e->getMessage()]);
            }
        }

        return redirect()->route('myPosts');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Post $post)
    {
        if ($post->user_id !== Auth::id()) {
            abort(403);
        }
        $post->delete();

        return redirect()->route('dashboard');
    }

    public function category(Category $category)
    {
        $query = $category->posts()
            ->where('published_at', '<=', now())
            ->with(['user', 'media'])
            ->withCount('claps')
            ->withCount('comments')
            ->latest();

        $posts = $query->paginate(10)->withQueryString();

        return view('post.index', [
            'posts' => $posts,
            'currentCategory' => $category,
        ]);
    }

    /**
     * Search posts by title or content
     */
    public function search(Request $request)
    {
        $query = $request->get('q', '');
        
        if (empty($query) || strlen($query) < 2) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'posts' => [
                        'data' => [],
                        'current_page' => 1,
                        'per_page' => 5,
                        'total' => 0,
                    ]
                ]);
            }
            return view('post.search', [
                'posts' => Post::query()->whereRaw('1 = 0')->paginate(10)->withQueryString(),
                'query' => $query,
            ]);
        }

        $posts = Post::where('published_at', '<=', now())
            ->where(function($q) use ($query) {
                $q->where('title', 'LIKE', "%{$query}%")
                  ->orWhere('content', 'LIKE', "%{$query}%");
            })
            ->with(['user', 'media', 'category'])
            ->withCount('claps')
            ->withCount('comments')
            ->latest()
            ->paginate(10)
            ->withQueryString();

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'posts' => $posts,
            ]);
        }

        return view('post.search', [
            'posts' => $posts,
            'query' => $query,
        ]);
    }

    public function myPosts()
    {
        $user = Auth::user();
        $posts = $user->posts()
            ->with(['user', 'media'])
            ->withCount('claps')
            ->withCount('comments')
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('post.index', [
            'posts' => $posts,
            'hideCategories' => true,
        ]);
    }
}
