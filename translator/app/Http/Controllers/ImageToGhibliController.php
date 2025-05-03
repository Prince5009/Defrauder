<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class ImageToGhibliController extends Controller
{
    public function index()
    {
        return view('imagetoghibli');
    }

    public function convert(Request $request)
    {
        $request->validate([
            'image' => 'required|image|max:20480',
        ]);

        $image = $request->file('image');

        // Send image to FastAPI backend
        $response = Http::attach(
            'image', file_get_contents($image), $image->getClientOriginalName()
        )->post('http://69.62.77.164:8000/image-ghibli/conversion/');

        // Handle response
        if ($response->successful()) {
            $filename = 'ghibli_' . time() . '.png';
            Storage::disk('public')->put($filename, $response->body());

            return back()->with('converted_image', asset('storage/' . $filename));
        } else {
            return back()->withErrors(['error' => 'Conversion failed. Please try again.']);
        }
    }
}
