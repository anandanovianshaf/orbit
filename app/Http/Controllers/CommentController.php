<?php

namespace App\Http\Controllers;

use App\Http\Requests\CommentStoreRequest;
use App\Models\Comment;
use App\Models\Post;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    /**
     * Store a newly created comment.
     */
    public function store(CommentStoreRequest $request, Post $post): RedirectResponse
    {
        $data = $request->validated();
        $data['user_id'] = Auth::id();
        $data['post_id'] = $post->id;

        Comment::create($data);

        return redirect()->route('post.show', [
            'username' => $post->user->username,
            'post' => $post->slug
        ])->with('success', 'Comment posted successfully!');
    }

    /**
     * Remove the specified comment.
     */
    public function destroy(Comment $comment): RedirectResponse
    {
        $post = $comment->post;
        
        // Authorization: Only post owner or comment owner can delete
        if ($comment->user_id !== Auth::id() && $post->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $comment->delete();

        return redirect()->route('post.show', [
            'username' => $post->user->username,
            'post' => $post->slug
        ])->with('success', 'Comment deleted successfully!');
    }
}
