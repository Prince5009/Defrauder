<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;
use App\Models\DownloadedContent;

class MergePdfController extends Controller
{
    public function index()
    {
        return view('mergepdf');
    }

    public function convert(Request $request)
    {
        try {
            // Validate the request
            $request->validate([
                'pdf_file1' => 'required|file|mimes:pdf|max:10240', // 10MB max
                'pdf_file2' => 'required|file|mimes:pdf|max:10240', // 10MB max
            ]);

            // Get both PDF files
            $file1 = $request->file('pdf_file1');
            $file2 = $request->file('pdf_file2');
            
            // Store original filenames in session
            session([
                'original_filenames' => $file1->getClientOriginalName() . ',' . $file2->getClientOriginalName()
            ]);
            
            // Create HTTP request with both files
            $response = Http::timeout(300)
                ->attach('pdfs', fopen($file1->getRealPath(), 'r'), $file1->getClientOriginalName())
                ->attach('pdfs', fopen($file2->getRealPath(), 'r'), $file2->getClientOriginalName())
                ->post('http://69.62.77.164:8000/pdf-to-pdf/merge/');

            if ($response->successful()) {
                // Get the PDF content from the response
                $pdfContent = $response->body();
                
                if (empty($pdfContent)) {
                    Log::error('Empty PDF content received from API');
                    return redirect()->back()
                        ->with('error', 'Failed to merge PDFs: Empty response received.')
                        ->withInput();
                }
                
                // Generate a unique filename
                $mergedFileName = 'merged_' . time() . '.pdf';
                
                // Ensure the storage directory exists
                $directory = storage_path('app/public/merged');
                if (!file_exists($directory)) {
                    mkdir($directory, 0755, true);
                }
                
                // Save the PDF file
                Storage::disk('public')->put('merged/' . $mergedFileName, $pdfContent);
                
                // Store the filename in session
                session(['merged_file' => $mergedFileName]);
                
                return redirect()->back()
                    ->with('message', 'PDFs merged successfully!');
            }
            
            // Log error if request was not successful
            Log::error('API Error:', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);
            
            return redirect()->back()
                ->with('error', 'Failed to merge PDFs. Please try again.')
                ->withInput();
            
        } catch (\Exception $e) {
            Log::error('PDF merge error: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            
            return redirect()->back()
                ->with('error', 'An error occurred while merging PDFs. Please try again.')
                ->withInput();
        }
    }

    public function download($filename)
    {
        try {
            // Directly fetch the file from the API and stream to user
            $response = Http::timeout(60)->get('http://69.62.77.164:8000/api/mergepdf/download/' . $filename);
            if ($response->successful()) {
                return response($response->body())
                    ->header('Content-Type', $response->header('Content-Type') ?? 'application/pdf')
                    ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
            }
            return back()->with('error', 'File not found or could not be downloaded.');
        } catch (\Exception $e) {
            \Log::error('Download error:', [
                'message' => $e->getMessage(),
                'file' => $filename
            ]);
            return back()->with('error', 'Failed to download file: ' . $e->getMessage());
        }
    }
}
