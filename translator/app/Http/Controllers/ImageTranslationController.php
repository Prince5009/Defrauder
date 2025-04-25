<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;

class ImageTranslationController extends Controller
{
    public function index()
    {
        return view('home');
    }

    public function extractText(Request $request)
    {
        try {
            $request->validate([
                'image' => 'required|image|mimes:jpeg,png,jpg,webp|max:20480',
            ]);

            $response = Http::attach(
                'image',
                file_get_contents($request->file('image')->getRealPath()),
                $request->file('image')->getClientOriginalName()
            )->post('http://127.0.0.1:8001/api/extract-word/');

            $data = $response->json();

            // Log the response for debugging
            \Log::info('API Response:', $data);

            if ($response->successful() && isset($data['status']) && $data['status'] === 'success') {
                $extractedText = trim($data['extracted_text'] ?? '');
                
                if (empty($extractedText)) {
                    return back()->with('error', 'No text was found in the image');
                }

                // Store both the extracted text and status
                return back()->with([
                    'extractedText' => $extractedText,
                    'status' => 'success'
                ]);
            }

            return back()->with('error', 'Failed to extract text from image');

        } catch (\Exception $e) {
            \Log::error('Error in text extraction: ' . $e->getMessage());
            return back()->with('error', 'Error processing image');
        }
    }

    private function sendResponse(Request $request, array $data, string $type, int $status = 200)
    {
        if ($request->wantsJson()) {
            return response()->json($data, $status);
        }

        // For regular form submissions, use redirect with flash data
        return back()->with($data);
    }
}
