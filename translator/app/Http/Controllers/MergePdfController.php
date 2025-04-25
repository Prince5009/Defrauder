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
                ->post('http://127.0.0.1:8001/pdf-to-pdf/merge/');

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
            $filePath = storage_path('app/public/merged/' . $filename);
            
            if (!file_exists($filePath)) {
                Log::error('File not found: ' . $filePath);
                return redirect()->back()->with('error', 'File not found.');
            }
            
            // Record the download in database
            DownloadedContent::create([
                'user_id' => Auth::id(),
                'original_filename' => session('original_filenames', 'unknown.pdf'),
                'converted_file' => $filename,
                'downloaded_at' => now()
            ]);
            
            Log::info('Downloading file: ' . $filePath);
            
            // Return the file as a download response
            return response()->download($filePath, $filename, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
                'Content-Length' => filesize($filePath)
            ]);
            
        } catch (\Exception $e) {
            Log::error('Download error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to download file: ' . $e->getMessage());
        }
    }
}
