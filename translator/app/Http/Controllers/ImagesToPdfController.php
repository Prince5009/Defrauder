<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ImagesToPdfController extends Controller
{
    public function index()
    {
        return view('imagestopdf');
    }

    public function convert(Request $request)
    {
        try {
            $numberOfImages = $request->input('number_of_images');
            $formData = [];
            
            // Collect all uploaded images
            for ($i = 1; $i <= $numberOfImages; $i++) {
                if ($request->hasFile('image' . $i)) {
                    $formData['image' . $i] = $request->file('image' . $i);
                }
            }

            // Send request to API
            $response = Http::timeout(60)
                ->attach('number_of_images', $numberOfImages)
                ->withoutVerifying();

            // Attach each image to the request
            foreach ($formData as $key => $file) {
                $response = $response->attach(
                    $key,
                    file_get_contents($file->path()),
                    $file->getClientOriginalName()
                );
            }

            // Make the API call
            $apiResponse = $response->post('http://69.62.77.164:8000/api/imagepdf/pdf/');

            if ($apiResponse->successful()) {
                return response($apiResponse->body(), 200)
                    ->header('Content-Type', 'application/pdf')
                    ->header('Content-Disposition', 'attachment; filename="converted.pdf"');
            }

            return response()->json([
                'error' => 'Failed to convert images to PDF'
            ], 500);

        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
