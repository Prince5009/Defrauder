<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TextTranslateController extends Controller
{
    private $languageMap = [
        'hi' => 'hindi',
        'gu' => 'gujarati'
    ];

    public function index()
    {
        return view('text');
    }

    public function translate(Request $request)
    {
        try {
            $request->validate([
                'text' => 'required|string|max:10000',
                'language' => 'required|string|in:hi,gu',
            ]);

            // Map the short language code to full name
            $fullLanguage = $this->languageMap[$request->language];

            $response = Http::post('http://127.0.0.1:8001/text/translate/', [
                'text' => $request->text,
                'language' => $fullLanguage // Send full language name to API
            ]);

            if (!$response->successful()) {
                Log::error('Translation API error', [
                    'status' => $response->status(),
                    'response' => $response->body()
                ]);
                return response()->json([
                    'error' => 'Translation service is currently unavailable. Please try again later.'
                ], 500);
            }

            $result = $response->json();
            return response()->json([
                'translatedText' => $result['translated_text'] ?? 'Translation failed'
            ]);

        } catch (\Exception $e) {
            Log::error('Translation error: ' . $e->getMessage());
            return response()->json([
                'error' => 'An error occurred during translation. Please try again.'
            ], 500);
        }
    }
}
