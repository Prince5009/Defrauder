<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class AudioToTextController extends Controller
{
    public function index()
    {
        return view('audiototext');
    }

    public function convert(Request $request)
    {
        try {
            $file = $request->file('audio');
            
            if (!$file) {
                return response()->json(['error' => 'No file uploaded'], 400);
            }

            $response = Http::withoutVerifying()
                ->timeout(300)
                ->attach('file', file_get_contents($file->path()), $file->getClientOriginalName())
                ->post('http://127.0.0.1:8001/api/speech/text/');

            return $response->json();
            
        } catch (\Exception $e) {
            if (strpos($e->getMessage(), 'Operation timed out') !== false) {
                return response()->json([
                    'error' => 'The request is taking longer than expected. Please try with a shorter audio file or try again later.',
                    'details' => 'Request timeout after 5 minutes'
                ], 504);
            }

            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
} 