<?php

namespace App\Http\Controllers;

use App\Models\Media;
use App\Models\Post;
use App\Models\PostAuthor;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Spatie\Image\Image;

class LogController extends BaseController
{
    public function index(Request $request) {
        $posts = Post::all();

        $tags = null;
        $users = null;
        $currentUserId = null;
        if(Auth::check()) {
            foreach($posts as $post) {
                $post->authors = $post->postAuthors->map(function($author) { return $this->getProfileById($author->user_id); })->toArray();
            }

            $tags = Tag::all();
            $users = $this->getUsers();
            $currentUserId = $this->getProfile()->id;
        }

        return view('log/index', ['posts' => $posts, 'users' => $users, 'tags' => $tags, 'current_user_id' => $currentUserId]);
    }

    public function create(Request $request): \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
    {

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'authors_ids' => 'array',
            'authors_ids.*' => 'uuid',
            'tags' => 'nullable|array',
            'tags.*' => 'uuid|exists:tags,id',
            'media_files' => 'nullable|array',
            'media_files.*' => 'file|mimes:jpg,jpeg,png,heic|max:2048',
        ]);

        DB::beginTransaction();

        try {
            $post = new Post([
                'title' => $validated['title'],
                'submitter_id' => $this->getProfile()->id,
            ]);

            $post->save();

            foreach($validated['authors_ids'] as $authorId) {
                $postAuthor = new PostAuthor([
                    'user_id' => $authorId,
                    'post_id' => $post->id
                ]);
                $postAuthor->save();
            }

            if (!empty($validated['tags'])) {
                $post->tags()->attach($validated['tags']);
            }

            if ($request->hasFile('media_files')) {
                foreach ($request->file('media_files') as $file) {

                    $extension = $file->getClientOriginalExtension();

                    $media = new Media([
                        'extension' => $extension === 'heic' ? 'jpg' : $extension,
                        'size' => $file->getSize()
                    ]);

                    $media->post()->associate($post);
                    $media->save();


                    if ($extension === 'heic') {
                        $imagePath = storage_path('app/public/media/' . $media->id.'.jpg');
                        Image::load($file->getPathname())->save($imagePath);
                    } else {
                        $file->storeAs('media', $media->id.'.'.$extension, 'public');
                    }
                }
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            abort(422, $e->getMessage());
        }

        return redirect(route("index"));
    }

    public function show(Request $request, string $id) {
        $post = Post::where('id', $id)->first();
        if($post) {

            $currentUserId = null;
            if(Auth::check()) {
                $post->authors = $post->postAuthors->map(function ($author) {
                    return $this->getProfileById($author->user_id);
                })->toArray();
                $currentUserId = $this->getProfile()->id;
            }
            return view('log/show', ['post' => $post, 'current_user_id' => $currentUserId]);
        }

        abort(404);
    }

    public function delete(Request $request, string $id): \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
    {
        $post = Post::where('id', $id)->first();
        $authorsIds = $post->postAuthors->map(function($author) { return $this->getProfileById($author->user_id)->id; })->toArray();
        $currentUserId = $this->getProfile()->id;
        if(in_array($currentUserId, $authorsIds) || $post->submitter_id == Auth::id()) {
            $post->delete();
        }

        return redirect(route("index"));
    }

    protected function getToken() {
        $response = Http::asForm()->post(env('KEYCLOAK_BASE_URL', '').'/realms/master/protocol/openid-connect/token', [
            "grant_type" => "client_credentials",
            "client_id" => "admin-cli",
            "client_secret" => env('KEYCLOAK_ADMINCLI_SECRET', '')
        ]);
        return $response->ok() && isset($response->json()['access_token']) ? $response->json()['access_token'] : null;
    }

    protected function getProfile(): object|bool
    {
        $token = $this->getToken();
        if($token) {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer '.$token
            ])->get(env('KEYCLOAK_BASE_URL', '').'/admin/realms/'.env('KEYCLOAK_REALM', 'master').'/users', [
                "email" => Auth::id()
            ]);
            return $response->ok() && isset($response->json()[0]) ? (object)$response->json()[0] : false;
        }
        return false;
    }

    protected function getProfileById(string $id): object|bool
    {
        $token = $this->getToken();
        if($token) {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer '.$token
            ])->get(env('KEYCLOAK_BASE_URL', '').'/admin/realms/'.env('KEYCLOAK_REALM', 'master').'/users/'.$id);

            return $response->ok() ? (object)$response->json() : false;
        }
        return false;
    }

    protected function getUsers(): array | bool
    {
        $token = $this->getToken();
        if($token) {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer '.$token
            ])->get(env('KEYCLOAK_BASE_URL', '').'/admin/realms/'.env('KEYCLOAK_REALM', 'master').'/users?enabled=true');

            if($response->ok()) {
                return array_map(function($user) { return (object)$user; }, $response->json());
            }
        }
        return false;
    }
}
