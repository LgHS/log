<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Auth;

class LogController extends BaseController
{
    public function index(Request $request) {
        dd(Post::all());
    }

    public function create(Request $request) {

    }

    public function show(Request $request, string $id) {

    }

    public function delete(Request $request, string $id) {

    }
}
