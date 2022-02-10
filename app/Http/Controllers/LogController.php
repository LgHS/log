<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Auth;

class LogController extends BaseController
{
    public function test(Request $request) {

        dd(Auth::user());
    }
}
