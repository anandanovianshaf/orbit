<?php

use App\Http\Controllers\ClapController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\FollowerController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PublicProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/test-image', function () {
    $post = \App\Models\Post::with('media')->first();
    if (!$post) {
        return 'No post found';
    }
    
    $media = $post->getFirstMedia();
    if (!$media) {
        return 'No media found for post ID: ' . $post->id;
    }
    
    $imageUrl = $post->imageUrl();
    $mediaUrl = $media->getUrl();
    $mediaPath = $media->getPath();
    
    return [
        'post_id' => $post->id,
        'post_title' => $post->title,
        'media_id' => $media->id,
        'media_url_from_library' => $mediaUrl,
        'imageUrl_method' => $imageUrl,
        'media_path' => $mediaPath,
        'file_exists' => file_exists($mediaPath),
        'disk' => $media->disk,
        'collection_name' => $media->collection_name,
        'file_name' => $media->file_name,
        'app_url' => config('app.url'),
        'storage_url_config' => config('filesystems.disks.public.url'),
        'test_direct_url' => config('app.url') . '/storage/' . str_replace(storage_path('app/public'), '', $mediaPath),
    ];
});

Route::get('/@{user:username}', [PublicProfileController::class, 'show'])
    ->name('profile.show');

Route::get('/', [PostController::class, 'index'])
    ->name('dashboard');

Route::get('/@{username}/{post:slug}', [PostController::class, 'show'])
    ->name('post.show');

Route::get('/category/{category}', [PostController::class, 'category'])
    ->name('post.byCategory');

Route::get('/search', [PostController::class, 'search'])
    ->name('post.search');

Route::view('/offline', 'offline')->name('offline');

Route::middleware(['auth', 'verified'])->group(function() {

    Route::get('/post/create', [PostController::class, 'create'])
        ->name('post.create');

    Route::post('/post/create', [PostController::class, 'store'])
        ->name('post.store');

    Route::get('/post/{post:slug}', [PostController::class, 'edit'])
        ->name('post.edit');

    Route::put('/post/{post}', [PostController::class, 'update'])
        ->name('post.update');

    Route::delete('/post/{post}', [PostController::class, 'destroy'])
        ->name('post.destroy');
        
    Route::get('/my-posts', [PostController::class, 'myPosts'])
        ->name('myPosts');

    Route::post('/follow/{user}', [FollowerController::class, 'followUnfollow'])
        ->name('follow');

    Route::post('/clap/{post}', [ClapController::class, 'clap'])
        ->name('clap');

    Route::post('/post/{post}/comment', [CommentController::class, 'store'])
        ->name('comment.store');

    Route::delete('/comment/{comment}', [CommentController::class, 'destroy'])
        ->name('comment.destroy');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
