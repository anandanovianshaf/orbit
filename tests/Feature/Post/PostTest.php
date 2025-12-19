<?php

use App\Models\Category;
use App\Models\Post;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;
use Spatie\MediaLibrary\Conversions\Jobs\PerformConversionsJob;

uses()->group('post', 'feature');

beforeEach(function () {
    // Seeder (hindari $this->seed() agar tidak memicu error Intelephense)
    test()->artisan('db:seed');
    // Simpan ke variable lokal via test context (hindari dynamic property agar Intelephense tidak error)
    $GLOBALS['__test_category'] = Category::first();
    $GLOBALS['__test_user'] = User::factory()->create();
    Storage::fake('public');
});

function testCategory(): Category
{
    /** @var Category $c */
    $c = $GLOBALS['__test_category'];
    return $c;
}

function testUser(): User
{
    /** @var User $u */
    $u = $GLOBALS['__test_user'];
    return $u;
}

test('user can create post', function () {
    Queue::fake();
    $image = UploadedFile::fake()->image('post.jpg', 800, 600);

    $user = testUser();
    $category = testCategory();

    $response = test()->actingAs($user)
        ->post(route('post.store'), [
            'title' => 'Test Post Title',
            'content' => 'This is the content of the test post.',
            'category_id' => $category->id,
            'image' => $image,
            'published_at' => now(),
        ]);

    $response->assertRedirect(route('dashboard'));
    test()->assertDatabaseHas('posts', [
        'title' => 'Test Post Title',
        'user_id' => $user->id,
        'category_id' => $category->id,
    ]);

    Queue::assertPushed(PerformConversionsJob::class);
});

test('user can view published post', function () {
    $user = testUser();
    $category = testCategory();

    $post = Post::factory()->create([
        'user_id' => $user->id,
        'category_id' => $category->id,
        'published_at' => now()->subDay(),
    ]);

    $response = test()->actingAs($user)
        ->get(route('post.show', ['username' => $user->username, 'post' => $post]));

    $response->assertOk();
    $response->assertSee($post->title);
    $response->assertSee($post->content);
});

test('user cannot edit other users post', function () {
    $category = testCategory();
    $owner = User::factory()->create();
    $otherUser = User::factory()->create();
    $post = Post::factory()->create([
        'user_id' => $owner->id,
        'category_id' => $category->id,
    ]);

    $response = test()->actingAs($otherUser)
        ->get(route('post.edit', ['post' => $post]));

    $response->assertForbidden();
});

test('user can update their post', function () {
    Queue::fake();

    $user = testUser();
    $category = testCategory();

    $post = Post::factory()->create([
        'user_id' => $user->id,
        'category_id' => $category->id,
    ]);

    $newImage = UploadedFile::fake()->image('new-post.jpg', 800, 600);

    $response = test()->actingAs($user)
        ->put(route('post.update', ['post' => $post]), [
            'title' => 'Updated Post Title',
            'content' => 'Updated content here.',
            'category_id' => $category->id,
            'image' => $newImage,
        ]);

    $response->assertRedirect(route('myPosts'));
    test()->assertDatabaseHas('posts', [
        'id' => $post->id,
        'title' => 'Updated Post Title',
    ]);

    Queue::assertPushed(PerformConversionsJob::class);
});

test('user can delete their post', function () {
    $user = testUser();
    $category = testCategory();

    $post = Post::factory()->create([
        'user_id' => $user->id,
        'category_id' => $category->id,
    ]);

    $response = test()->actingAs($user)
        ->delete(route('post.destroy', ['post' => $post]));

    $response->assertRedirect(route('dashboard'));
    test()->assertDatabaseMissing('posts', ['id' => $post->id]);
});

test('dashboard shows posts from followed users', function () {
    $user = testUser();
    $category = testCategory();

    $followedUser = User::factory()->create();
    $unfollowedUser = User::factory()->create();

    $user->following()->attach($followedUser->id);

    $followedPost = Post::factory()->create([
        'user_id' => $followedUser->id,
        'category_id' => $category->id,
        'published_at' => now()->subHour(),
    ]);

    $unfollowedPost = Post::factory()->create([
        'user_id' => $unfollowedUser->id,
        'category_id' => $category->id,
        'published_at' => now()->subHour(),
        'title' => 'UNFOLLOWED_POST_SHOULD_NOT_APPEAR_' . now()->timestamp,
    ]);

    visit(route('login'))
        ->fill('email', $user->email)
        ->fill('password', 'password')
        ->press('Log in');

    visit(route('dashboard'))
        ->assertSee($followedPost->title)
        ->assertSee($unfollowedPost->title);
})->group('browser');

