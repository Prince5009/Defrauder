<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class VideoToAudioController extends Controller
{
    public function index()
    {
        return view('video');
    }

    public function extractAudio(Request $request)
    {
        try {
            $request->validate([
                'video' => 'required|file|mimes:mp4,avi,mov,wmv|max:100000'
            ]);

            $video = $request->file('video');
            
            // Log the request details
            Log::info('Video to Audio conversion request received', [
                'original_name' => $video->getClientOriginalName(),
                'size' => $video->getSize(),
                'mime_type' => $video->getMimeType()
            ]);

            // Create multipart form data for the API request
            $response = Http::attach(
                'video', 
                file_get_contents($video->getRealPath()), 
                $video->getClientOriginalName()
            )->post('http://69.62.77.164:8000/api/audio/extract/');

            Log::info('API Response received', [
                'status' => $response->status(),
                'body' => $response->json()
            ]);

            if ($response->successful()) {
                $data = $response->json();
                
                if (isset($data['audio_file'])) {
                    return response()->json([
                        'success' => true,
                        'message' => 'Audio extracted successfully',
                        'audio_file' => $data['audio_file']
                    ]);
                }
                
                return response()->json([
                    'success' => false,
                    'message' => 'No audio file in response',
                    'debug_response' => $data  // Add this for debugging
                ], 400);
            }

            return response()->json([
                'success' => false,
                'message' => 'Failed to convert video to audio: ' . ($response->json()['error'] ?? 'Unknown error'),
                'debug_response' => $response->json()  // Add this for debugging
            ], $response->status());

        } catch (\Exception $e) {
            Log::error('Error in video to audio conversion:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error processing video: ' . $e->getMessage()
            ], 500);
        }
    }

    public function download($filename)
    {
        try {
            Log::info('Attempting to download audio file', ['filename' => $filename]);
            
            $response = Http::timeout(60)->get("http://69.62.77.164:8000/api/audio/download/{$filename}");
            
            Log::info('Download response received', [
                'status' => $response->status(),
                'headers' => $response->headers(),
                'content_type' => $response->header('Content-Type'),
                'content_length' => $response->header('Content-Length')
            ]);
            
            if ($response->successful()) {
                $contentType = $response->header('Content-Type') ?? 'audio/wav';
                return response($response->body())
                    ->header('Content-Type', $contentType)
                    ->header('Content-Disposition', 'attachment; filename="' . $filename . '"')
                    ->header('Content-Length', strlen($response->body()));
            }

            // Try to get error message from response
            $errorMessage = 'Unknown error';
            try {
                $jsonResponse = $response->json();
                $errorMessage = $jsonResponse['error'] ?? $jsonResponse['message'] ?? 'Unknown error';
            } catch (\Exception $e) {
                $errorMessage = 'Failed to parse error response';
            }

            Log::error('Failed to download audio file', [
                'status' => $response->status(),
                'error' => $errorMessage,
                'response_body' => $response->body()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to download audio file: ' . $errorMessage
            ], $response->status());

        } catch (\Exception $e) {
            Log::error('Error downloading audio file:', [
                'filename' => $filename,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error downloading audio file: ' . $e->getMessage()
            ], 500);
        }
    }
} 