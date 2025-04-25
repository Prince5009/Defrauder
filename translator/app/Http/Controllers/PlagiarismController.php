<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\UserNotification;

class PlagiarismController extends Controller
{
    public function index()
    {
        return view('plagiarism');
    }

    public function check(Request $request)
    {
        try {
            // Validate uploaded files
            $request->validate([
                'pdf1' => 'required|file|mimes:pdf|max:10240',
                'pdf2' => 'required|file|mimes:pdf|max:10240',
            ]);

            $pdf1 = $request->file('pdf1');
            $pdf2 = $request->file('pdf2');

            // Log the request details
            Log::info('Sending request to Plagiarism API', [
                'pdf1_name' => $pdf1->getClientOriginalName(),
                'pdf1_size' => $pdf1->getSize(),
                'pdf2_name' => $pdf2->getClientOriginalName(),
                'pdf2_size' => $pdf2->getSize(),
            ]);

            // Send PDFs to backend API
            $response = Http::attach(
                'pdf1',
                file_get_contents($pdf1->getRealPath()),
                $pdf1->getClientOriginalName()
            )->attach(
                'pdf2',
                file_get_contents($pdf2->getRealPath()),
                $pdf2->getClientOriginalName()
            )->post('http://127.0.0.1:8001/detect/check-plagiarism/');

            // Log the response for debugging
            Log::info('Plagiarism API Response', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);

            if (!$response->successful()) {
                Log::error('Plagiarism check failed', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                
                $errorMessage = 'Failed to check plagiarism. ';
                if ($response->status() === 404) {
                    $errorMessage .= 'API endpoint not found. Please check if the backend service is running.';
                } elseif ($response->status() === 400) {
                    $errorMessage .= $response->json()['error'] ?? 'Invalid request. Please check your PDF files.';
                } else {
                    $errorMessage .= 'Please try again. Error: ' . $response->body();
                }
                
                return back()->with('error', $errorMessage);
            }

            $result = $response->json();
            
            // Validate the response structure
            if (!isset($result['plagiarism_percentage'])) {
                Log::error('Invalid response format', ['response' => $result]);
                return back()->with('error', 'Invalid response from plagiarism service. Expected percentage not found.');
            }

            $percentage = $result['plagiarism_percentage'];
            
            // Get logged-in user
            $user = Auth::user();

            // Prepare email data
            $emailData = [
                'user' => $user,
                'file1' => $pdf1->getClientOriginalName(),
                'file2' => $pdf2->getClientOriginalName(),
                'percentage' => $percentage,
            ];

            // Send the email
            Mail::to($user->email)->send(new UserNotification($emailData));

            // Send response back to view with more detailed information
            return back()->with([
                'success' => true,
                'plagiarism_percentage' => $percentage,
                'file1_name' => $pdf1->getClientOriginalName(),
                'file2_name' => $pdf2->getClientOriginalName(),
                'email_success' => '📬 Plagiarism result has been sent to your email successfully!',
                'message' => "Plagiarism check completed! The documents are {$percentage} similar."
            ]);

        } catch (\Exception $e) {
            Log::error('Plagiarism check error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return back()->with('error', 'An error occurred while checking plagiarism. Please try again.');
        }
    }
}
