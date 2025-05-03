<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class QRController extends Controller
{
    public function index()
    {
        return view('qr');
    }

    public function generate(Request $request)
    {
        $request->validate([
            'link' => 'required|string|max:2048'
        ]);

        try {
            // Log the full request data
            Log::info('QR Code Request Data:', [
                'link' => $request->link,
                'request_data' => $request->all()
            ]);

            // Create the request data in the format Django expects
            $requestData = ['link' => $request->link];
            Log::info('Sending request to Django:', $requestData);

            $response = Http::asForm()->post('http://69.62.77.164:8000/qr/generate/', $requestData);

            // Log the response status and content
            Log::info('Django Response:', [
                'status' => $response->status(),
                'content' => $response->body()
            ]);

            if ($response->successful()) {
                Log::info('QR code generated successfully');
                return response($response->body())
                    ->header('Content-Type', 'image/png')
                    ->header('Access-Control-Allow-Origin', '*');
            }

            // If we get a 400 error, log the error response
            if ($response->status() === 400) {
                Log::error('Django API returned 400:', [
                    'response' => $response->json() ?? $response->body()
                ]);
                return response()->json([
                    'error' => 'Invalid request data',
                    'details' => $response->json()['error'] ?? 'The API rejected the request'
                ], 400);
            }

            Log::error('QR generation failed with status: ' . $response->status());
            return response()->json([
                'error' => 'Failed to generate QR code',
                'status' => $response->status(),
                'details' => $response->body()
            ], 500);

        } catch (\Exception $e) {
            Log::error('QR generation error: ' . $e->getMessage());
            return response()->json([
                'error' => 'Failed to connect to QR service',
                'message' => $e->getMessage()
            ], 500);
        }
    }
} 