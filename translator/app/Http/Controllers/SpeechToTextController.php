<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SpeechToTextController extends Controller
{
    public function index()
    {
        return view('speechtotext');
    }

    public function convert(Request $request)
    {
        // Logic to convert speech to text
        return back()->with('message', 'Speech converted to text successfully.');
    }
}
