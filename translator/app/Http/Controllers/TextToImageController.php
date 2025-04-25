<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TextToImageController extends Controller
{
    public function index()
    {
        return view('texttoimage');
    }

    public function generate(Request $request)
    {
        $request->validate([
            'text' => 'required|string|max:1000',
        ]);

        $text = $request->input('text');

        // Simulate image generation from text
        $generatedImagePath = 'path/to/generated/image.jpg';

        return back()->with('generated_image', $generatedImagePath);
    }
}
