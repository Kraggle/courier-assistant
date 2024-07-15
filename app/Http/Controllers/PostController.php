<?php

namespace App\Http\Controllers;

use App\Helpers\K;
use App\Models\Tag;
use App\Models\Post;
use App\Models\Media;
use App\Models\Category;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class PostController extends FilesController {
    /**
     * Display a listing of the news.
     * 
     * @return \Illuminate\Http\Response
     */
    public function newsIndex() {
        return view('post.index', ['type' => 'news']);
    }

    /**
     * Display a news post.
     * 
     * @param string  $slug
     * 
     * @return \Illuminate\Http\Response
     */
    public function showNews(string $slug) {
        return view('post.show', ['type' => 'news', 'slug' => $slug]);
    }

    /**
     * Display a listing of the tips.
     * 
     * @return \Illuminate\Http\Response
     */
    public function tipsIndex() {
        return view('post.index', ['type' => 'tips']);
    }

    /**
     * Display a tip post.
     * 
     * @param string  $slug
     * 
     * @return \Illuminate\Http\Response
     */
    public function showPost(string $slug) {
        return view('post.show', ['type' => 'tips', 'slug' => $slug]);
    }

    /**
     * Show the post creation page.
     * 
     * @return \Illuminate\Http\Response
     */
    public function creator() {
        if (Gate::denies('is-admin'))
            return back()->with('error', 'You do not have permission to create posts.');

        // $model = '\App\Models\Category';
        // $new = 'Blood';
        // K::log(call_user_func_array("$model::where", ['name', $new])->count());
        // call_user_func("$model::create", ['name' => $new]);

        return view('post.creator', [
            'categories' => Category::all()->sortBy('name'),
            'tags' => Tag::all()->sortBy('name'),
            'editmode' => false
        ]);
    }

    /**
     * Show the post editor page.
     * 
     * @return \Illuminate\Http\Response
     */
    public function editor(Post $post) {
        if (Gate::denies('is-admin'))
            return back()->with('error', 'You do not have permission to create posts.');

        // K::log(Category::where('name', 'Test')->count());

        return view('post.creator', [
            'post' => $post,
            'categories' => Category::all()->sortBy('name'),
            'tags' => Tag::all()->sortBy('name'),
            'editmode' => true
        ]);
    }

    /**
     * Store a newly created post in storage.
     * 
     * @param  \Illuminate\Http\Request  $request
     */
    public function create(Request $request) {
        $request->validate([
            'slug' => ['required', 'string', 'unique:posts'],
            'title' => ['required', 'string'],
            'type' => ['required', 'string'],
            'banner' => 'string',
            'categories' => 'array',
            'tags' => 'array',
            'content' => 'string',
        ]);

        $user = $request->user();
        $post = $user->posts()->create(K::merge($request->all(), [
            'slug' => Str::slug($request->slug),
            'is_live' => K::isTrue($request->is_live ?? 0)
        ]));

        $this->setAttached($post, 'Category', $request->input('categories', []));
        $this->setAttached($post, 'Tag', $request->input('tags', []));

        return redirect()->route('post.editor', $post->id)->with('status', 'Post created successfully.');
    }

    /**
     * Update an existing post in storage.
     * 
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Post  $post
     */
    public function update(Request $request, Post $post) {
        $request->validate([
            'slug' => ['required', 'string'],
            'title' => ['required', 'string'],
            'type' => ['required', 'string'],
            'banner' => 'string',
            'categories' => 'array',
            'tags' => 'array',
            'content' => 'string',
        ]);

        $post->update(K::merge($request->all(), [
            'slug' => Str::slug($request->slug),
            'is_live' => K::isTrue($request->is_live ?? 0)
        ]));

        $this->setAttached($post, 'Category', $request->input('categories', []));
        $this->setAttached($post, 'Tag', $request->input('tags', []));

        return back()->with('success', 'Post updated successfully.');
    }

    /**
     * Reset posts tags or categories.
     * 
     * @param Post $post
     * @param string $model
     * @param array<string> $toAdd
     * 
     * @return void
     */
    public function setAttached(Post $post, string $model, array $add) {
        $func = ['Category' => 'categories', 'Tag' => 'tags'][$model];
        $model = '\App\Models\\' . $model;

        $post->{$func}()->detach();
        foreach ($add as $new) {
            $new = Str::title($new);
            if (!call_user_func_array("$model::where", ['name', $new])->count())
                call_user_func("$model::create", ['name' => $new]);

            $c = call_user_func_array("$model::where", ['name', $new])->first();
            $post->{$func}()->attach($c->id);
        }
    }

    /**
     * Silently upload a media file.
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function upload(Request $request) {
        $link = $request->hasFile('image') ?
            $this->uploadFile($request->file('image'), "images/post", null, false) : null;

        if ($link) {
            Media::create(K::merge($request->all(), [
                'path' => $link
            ]));

            return response()->json(['success' => 'Media uploaded successfully.']);
        }

        return response()->json(['error' => 'No media uploaded.']);
    }
}
