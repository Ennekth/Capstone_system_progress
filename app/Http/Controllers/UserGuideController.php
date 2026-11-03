<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UserGuideController extends Controller
{
    public function index()
    {
        return view('user-guide.index');
    }
}