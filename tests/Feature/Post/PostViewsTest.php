<?php

use App\Models\Post;
use App\Models\Category;
use App\Models\User;

it('increments views once per session when viewing a post', function () {
    $user = User::factory()->create();
    $category = Category::create(['name' => 'AI']);

    /** @var Post $post */
    $post = Post::factory()->create([
        'user_id' => $user->id,
        'category_id' => $category->id,
        'views' => 0,
        'published_at' => now()->subMinute(),
    ]);

    // First view increments
    $this->get(route('post.show', ['username' => $user->username, 'post' => $post]))
        ->assertOk();

    expect($post->fresh()->views)->toBe(1);

    // Second view in same session does not increment
    $this->get(route('post.show', ['username' => $user->username, 'post' => $post]))
        ->assertOk();

    expect($post->fresh()->views)->toBe(1);
});
