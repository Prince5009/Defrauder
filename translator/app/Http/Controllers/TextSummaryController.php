<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TextSummaryController extends Controller
{
    public function index()
    {
        return view('textsummary');
    }

    public function summarize(Request $request)
    {
        try {
            $request->validate([
                'text' => 'required|string|max:10000',
                'percent' => 'required|integer|min:1|max:99',
            ]);

            $response = Http::post('http://69.62.77.164:8000/text-summarize/content/', [
                'text' => $request->text,
                'percent' => $request->percent
            ]);

            if (!$response->successful()) {
                Log::error('Summarization API error', [
                    'status' => $response->status(),
                    'response' => $response->body()
                ]);
                return response()->json([
                    'error' => 'Summarization service is currently unavailable. Please try again later.'
                ], 500);
            }

            $result = $response->json();
            return response()->json([
                'summary' => $result['summary'] ?? 'Summarization failed'
            ]);

        } catch (\Exception $e) {
            Log::error('Summarization error: ' . $e->getMessage());
            return response()->json([
                'error' => 'An error occurred during summarization. Please try again.'
            ], 500);
        }
    }
}
