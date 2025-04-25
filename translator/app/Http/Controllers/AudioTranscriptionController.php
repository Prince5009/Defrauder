<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AudioTranscriptionController extends Controller
{
    public function index()
    {
        return view('audio.index');
    }

    public function transcribe(Request $request)
    {
        try {
            $request->validate([
                'audio_file' => 'required|file|mimes:wav|max:10240', // max 10MB
            ]);

            $audioFile = $request->file('audio_file');
            
            // Create a multipart form request to the Django API
            $response = Http::attach(
                'audio_file',
                file_get_contents($audioFile->path()),
                $audioFile->getClientOriginalName()
            )->post('http://127.0.0.1:8001/api/transcribe-audio/');

            Log::info('Audio Transcription API Response:', [
                'status' => $response->status(),
                'body' => $response->json()
            ]);

            if ($response->successful()) {
                $data = $response->json();
                
                if (isset($data['transcription'])) {
                    return response()->json([
                        'success' => true,
                        'transcription' => $data['transcription']
                    ]);
                }
                
                return response()->json([
                    'success' => false,
                    'message' => 'No transcription found in the response'
                ], 422);
            }

            return response()->json([
                'success' => false,
                'message' => 'Failed to transcribe audio: ' . ($response->json()['error'] ?? 'Unknown error')
            ], $response->status());

        } catch (\Exception $e) {
            Log::error('Audio Transcription Error:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error processing audio file: ' . $e->getMessage()
            ], 500);
        }
    }
} 