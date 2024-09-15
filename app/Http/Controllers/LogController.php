<?php

namespace App\Http\Controllers;

use App\Models\Media;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LogController extends BaseController
{
    public function index(Request $request) {
        $posts = Post::all();


        return view('log/index', ['posts' => $posts]);
    }

    public function create(Request $request): void
    {

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'author_id' => 'required|uuid',
            'tags' => 'nullable|array',
            'tags.*' => 'uuid|exists:tags,id',
            'media_files' => 'nullable|array',
            'media_files.*' => 'file|mimes:jpg,jpeg,png,gif|max:2048',
        ]);

        DB::beginTransaction();
        try {
            $post = Post::create([
                'title' => $validated['title'],
                'submitter_id' => Auth::id(),
                'author_id' => $validated['author_id'],
            ]);

            if (!empty($validated['tags'])) {
                $post->tags()->attach($validated['tags']);
            }

            if ($request->hasFile('media_files')) {
                foreach ($request->file('media_files') as $file) {
                    $file->store('media', 'public');

                    $media = new Media([
                        'extension' => $file->getExtension(),
                        'size' => $file->getSize(),
                        'post_id' => $post->id,
                    ]);

                    $media->save();
                }
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            abort(422);
        }

        redirect("index");
    }

    public function show(Request $request, string $id) {
        $post = Post::where('id', $id)->first();
        if($post) {
            $tags = $post->tags;
            $medias = $post->medias;

            return view('log/show', ['post' => $post]);
        }

        abort(404);
    }

    public function delete(Request $request, string $id): void
    {
        $post = Post::where('id', $id)->first();
        if($post->author_id == Auth::id() || $post->submitter_id == Auth::id()) {
            $post->delete();
        }

        redirect("index");
    }
}
