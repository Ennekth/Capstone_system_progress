<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserGuideController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $role = $user ? $user->role : 'guest';

        return view('user-guide.index', compact('role'));
    }
}