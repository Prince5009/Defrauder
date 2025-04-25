<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class VideoTrimmerController extends Controller
{
    public function index()
    {
        return view('videotrimmer');
    }

    public function trim(Request $request)
    {
        // Logic to trim video
        return back()->with('message', 'Video trimmed successfully.');
    }
}
