<?php

namespace App\Http\Controllers;

use App\Http\Requests\PostRequest;
use App\Post;
use Illuminate\Http\Request;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return response()->json([
            'posts' => Post::withCount('comments')
                ->with('tags')
                ->latest()
                ->paginate(),
        ]);
    }

    /**
     * load popular posts
     *
     * @return Response
     */
    public function loadPopular()
    {
        return response()->json([
            'posts' => Post::withCount('comments')
                ->latest()
                ->orderByDesc('comments_count')
                ->limit(4)
                ->get()
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(PostRequest $request)
    {
        $postAttr = $request->validated();

        $post = Post::create([
            'user_id' => auth()->id(),
        ] + $postAttr);

        return response()->json($post, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function show(Post $post)
    {
        return response()->json($post);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function update(PostRequest $request, Post $post)
    {
        $updated = $post->update($request->validated());

        return $updated ? response()->noContent() : response();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function destroy(Post $post)
    {
        return $post->delete() ?
            response()->noContent() :
            response();
    }
}
