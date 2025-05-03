<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Models\DownloadedContent;

class PdfToWordController extends Controller
{
    public function index()
    {
        return view('pdftoword');
    }

    public function convert(Request $request)
    {
        $request->validate([
            'pdf_file' => 'required|file|mimes:pdf|max:10240'
        ]);

        try {
            // Get the PDF file
            $pdfFile = $request->file('pdf_file');
            
            // Store original filename in session
            session(['pdf_original_filename' => $pdfFile->getClientOriginalName()]);
            
            // Log the file details for debugging
            Log::info('File details:', [
                'name' => $pdfFile->getClientOriginalName(),
                'size' => $pdfFile->getSize(),
                'mime' => $pdfFile->getMimeType()
            ]);

            // Create a multipart request
            $response = Http::withHeaders([
                'Accept' => 'application/json',
            ])->attach(
                'file', // The key should match what the API expects
                file_get_contents($pdfFile->getRealPath()),
                $pdfFile->getClientOriginalName()
            )->post('http://69.62.77.164:8000/pdf-to-word/convert/');

            // Log the API response for debugging
            Log::info('API Response:', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);

            if ($response->successful()) {
                // Get the Word file content from the response
                $wordContent = $response->body();
                
                // Generate a unique filename
                $wordFilename = pathinfo($pdfFile->getClientOriginalName(), PATHINFO_FILENAME) . '.docx';
                
                // Store the Word file
                Storage::disk('public')->put('converted/' . $wordFilename, $wordContent);
                
                // Return the view with the file information
                return back()->with([
                    'message' => 'PDF converted to Word successfully.',
                    'word_file' => $wordFilename
                ]);
            } else {
                // Log the error details
                Log::error('API Error:', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                
                return back()->with('error', 'Conversion failed. Error: ' . $response->body());
            }
        } catch (\Exception $e) {
            // Log the exception
            Log::error('Conversion Error:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }

    public function download($filename)
    {
        try {
            // Directly fetch the file from the API and stream to user
            $response = Http::timeout(60)->get('http://69.62.77.164:8000/api/pdftoword/download/' . $filename);
            if ($response->successful()) {
                return response($response->body())
                    ->header('Content-Type', $response->header('Content-Type') ?? 'application/vnd.openxmlformats-officedocument.wordprocessingml.document')
                    ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
            }
            return back()->with('error', 'File not found or could not be downloaded.');
        } catch (\Exception $e) {
            \Log::error('Download Error:', [
                'message' => $e->getMessage(),
                'file' => $filename
            ]);
            return back()->with('error', 'Error downloading file.');
        }
    }
}