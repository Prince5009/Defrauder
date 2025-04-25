<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Exception;

class TranslationController extends Controller
{
    /**
     * Handle image upload, simulate translation, and redirect with feedback.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function translate(Request $request)
    {
        $imagePath = null;
        try {
            // Validate request (matching the form fields)
            $request->validate([
                'source_lang' => 'required|string',
                'target_lang' => 'required|string',
                'image' => 'required|image|mimes:jpeg,png,jpg|max:20480' // Max 20MB
            ]);

            // Store the uploaded image temporarily just to confirm upload works
            // We'll delete it immediately after
            $imageFile = $request->file('image');
            $imagePath = $imageFile->store('temp_images', 'local');

            // Simulate successful processing
            $sourceLang = $request->input('source_lang');
            $targetLang = $request->input('target_lang');
            $originalFilename = $imageFile->getClientOriginalName();

            // Clean up the temporary image immediately
            if ($imagePath && Storage::disk('local')->exists($imagePath)) {
                Storage::disk('local')->delete($imagePath);
            }

            // Prepare a dummy success message
            $successMessage = sprintf(
                "Image '%s' uploaded successfully. Simulated translation from [%s] to [%s]. (Backend integration needed)",
                $originalFilename,
                strtoupper($sourceLang),
                strtoupper($targetLang)
            );

            // Redirect back with the success message
            return redirect()->route('images')->with('message', $successMessage);

        } catch (\Illuminate\Validation\ValidationException $e) {
             // Handle validation errors specifically
             Log::warning('Image Upload Validation Failed: ' . $e->getMessage());
             // Redirect back with validation errors
             return redirect()->route('images')
                        ->withErrors($e->validator)
                        ->withInput();

        } catch (Exception $e) {
            // Log unexpected errors
            Log::error('Image Processing Error: ' . $e->getMessage());

            // Clean up the temporary image if it was stored before the error
            if ($imagePath && Storage::disk('local')->exists($imagePath)) {
                Storage::disk('local')->delete($imagePath);
            }

            // Redirect back with a generic error message
            return redirect()->route('images')
                       ->with('error', 'An unexpected error occurred while processing the image. Please try again.')
                       ->withInput(); // Keep old input in the form
        }
    }
}
