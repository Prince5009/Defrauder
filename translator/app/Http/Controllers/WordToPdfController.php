<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Models\DownloadedContent;

class WordToPdfController extends Controller
{
    public function index()
    {
        return view('wordtopdf');
    }

    public function convert(Request $request)
    {
        $request->validate([
            'word_file' => 'required|file|mimes:doc,docx|max:10240'
        ]);

        try {
            // Get the Word file
            $wordFile = $request->file('word_file');
            
            // Store original filename in session
            session(['word_original_filename' => $wordFile->getClientOriginalName()]);
            
            // Log the file details for debugging
            Log::info('File details:', [
                'name' => $wordFile->getClientOriginalName(),
                'size' => $wordFile->getSize(),
                'mime' => $wordFile->getMimeType()
            ]);

            // Create a multipart request
            $response = Http::withHeaders([
                'Accept' => 'application/json',
            ])->attach(
                'file', // The key should match what the API expects
                file_get_contents($wordFile->getRealPath()),
                $wordFile->getClientOriginalName()
            )->post('http://127.0.0.1:8001/word-to-pdf/convert/');

            // Log the API response for debugging
            Log::info('API Response:', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);

            if ($response->successful()) {
                // Get the PDF file content from the response
                $pdfContent = $response->body();
                
                // Generate a unique filename
                $pdfFilename = pathinfo($wordFile->getClientOriginalName(), PATHINFO_FILENAME) . '.pdf';
                
                // Store the PDF file
                Storage::disk('public')->put('converted/' . $pdfFilename, $pdfContent);
                
                // Return the view with the file information
                return back()->with([
                    'message' => 'Word file converted to PDF successfully!',
                    'pdf_file' => $pdfFilename,
                    'original_filename' => $wordFile->getClientOriginalName() // Store original filename in session
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
            if (Storage::disk('public')->exists('converted/' . $filename)) {
                // Record the download
                DownloadedContent::create([
                    'user_id' => Auth::id(),
                    'original_filename' => session('word_original_filename', 'unknown.docx'),
                    'converted_file' => $filename,
                    'downloaded_at' => now()
                ]);

                return Storage::disk('public')->download('converted/' . $filename);
            }
            return back()->with('error', 'File not found.');
        } catch (\Exception $e) {
            Log::error('Download Error:', [
                'message' => $e->getMessage(),
                'file' => $filename
            ]);
            return back()->with('error', 'Error downloading file.');
        }
    }
}
