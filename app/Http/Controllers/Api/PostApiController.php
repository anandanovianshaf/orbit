<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\Request;

class PostApiController extends Controller
{
    public function index(Request $request)
    {
        $perPage = (int) $request->integer('per_page', 10);
        $perPage = max(1, min(50, $perPage));

        $posts = Post::query()
            ->with(['user', 'media', 'category'])
            ->where('published_at', '<=', now())
            ->withCount(['claps', 'comments'])
            ->latest()
            ->paginate($perPage);

        return response()->json([
            'data' => $posts->through(fn (Post $post) => $this->postSummary($post)),
            'meta' => [
                'current_page' => $posts->currentPage(),
                'per_page' => $posts->perPage(),
                'last_page' => $posts->lastPage(),
                'total' => $posts->total(),
            ],
        ]);
    }

    public function show(string $slug)
    {
        $post = Post::query()
            ->where('slug', $slug)
            ->with(['user', 'media', 'category', 'comments.user'])
            ->withCount(['claps', 'comments'])
            ->firstOrFail();

        return response()->json([
            'data' => $this->postDetail($post),
        ]);
    }

    public function search(Request $request)
    {
        $q = (string) $request->get('q', '');
        $q = trim($q);

        $perPage = (int) $request->integer('per_page', 10);
        $perPage = max(1, min(50, $perPage));

        if ($q === '' || mb_strlen($q) < 2) {
            return response()->json([
                'data' => [],
                'meta' => [
                    'current_page' => 1,
                    'per_page' => $perPage,
                    'last_page' => 1,
                    'total' => 0,
                ],
            ]);
        }

        $posts = Post::query()
            ->with(['user', 'media', 'category'])
            ->where('published_at', '<=', now())
            ->where(function ($query) use ($q) {
                $query->where('title', 'like', "%{$q}%")
                    ->orWhere('content', 'like', "%{$q}%");
            })
            ->withCount(['claps', 'comments'])
            ->latest()
            ->paginate($perPage)
            ->appends(['q' => $q]);

        return response()->json([
            'data' => $posts->through(fn (Post $post) => $this->postSummary($post)),
            'meta' => [
                'current_page' => $posts->currentPage(),
                'per_page' => $posts->perPage(),
                'last_page' => $posts->lastPage(),
                'total' => $posts->total(),
                'query' => $q,
            ],
        ]);
    }

    private function postSummary(Post $post): array
    {
        return [
            'id' => $post->id,
            'title' => $post->title,
            'slug' => $post->slug,
            'excerpt' => mb_substr(trim((string) $post->content), 0, 200),
            'views' => (int) ($post->views ?? 0),
            'claps_count' => (int) ($post->claps_count ?? 0),
            'comments_count' => (int) ($post->comments_count ?? 0),
            'published_at' => optional($post->published_at)->toISOString(),
            'created_at' => optional($post->created_at)->toISOString(),
            'user' => [
                'id' => $post->user?->id,
                'name' => $post->user?->name,
                'username' => $post->user?->username,
            ],
            'category' => $post->category ? [
                'id' => $post->category->id,
                'name' => $post->category->name,
                'slug' => $post->category->slug ?? null,
            ] : null,
            'image' => [
                'url' => $post->imageUrl() ? url($post->imageUrl()) : null,
                'preview_url' => $post->imageUrl('preview') ? url($post->imageUrl('preview')) : null,
                'large_url' => $post->imageUrl('large') ? url($post->imageUrl('large')) : null,
            ],
            'links' => [
                'web' => url(sprintf('/@%s/%s', $post->user?->username, $post->slug)),
            ],
        ];
    }

    private function postDetail(Post $post): array
    {
        return [
            ...$this->postSummary($post),
            'content' => (string) $post->content,
            'comments' => $post->comments->map(function ($c) {
                return [
                    'id' => $c->id,
                    'content' => (string) $c->content,
                    'created_at' => optional($c->created_at)->toISOString(),
                    'user' => [
                        'id' => $c->user?->id,
                        'name' => $c->user?->name,
                        'username' => $c->user?->username,
                    ],
                ];
            })->values(),
        ];
    }
}
