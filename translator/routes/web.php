<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\VideoTranslateController;
use App\Http\Controllers\PdfTranslateController;
use App\Http\Controllers\TranslationController;
use App\Http\Controllers\TextTranslateController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\ImageTranslationController;
use App\Http\Controllers\VideoToAudioController;
use App\Http\Controllers\QRController;
use App\Http\Controllers\VerificationController;
use Illuminate\Support\Facades\Password;
use Illuminate\Http\Request;

// Additional Controllers for new modules
use App\Http\Controllers\WordToPdfController;
use App\Http\Controllers\PdfToWordController;
use App\Http\Controllers\MergePdfController;
use App\Http\Controllers\ImagesToPdfController;
use App\Http\Controllers\TextSummaryController;
use App\Http\Controllers\SpeechToTextController;
use App\Http\Controllers\PlagiarismController;
use App\Http\Controllers\ImageToGhibliController;
use App\Http\Controllers\AudioToTextController;
use App\Http\Controllers\AudioTranscriptionController;
use App\Http\Controllers\FeedbackController;
use App\Http\Controllers\AdminController;

// Video to Audio routes
Route::get('/video', [VideoToAudioController::class, 'index'])->name('video.index');
Route::post('/video/extract', [VideoToAudioController::class, 'extractAudio'])->name('video.extract');
Route::get('/audio/download/{filename}', [VideoToAudioController::class, 'download'])->name('audio.download');

// Public Routes
Route::get('/', [ImageTranslationController::class, 'index'])->name('home');
Route::post('/extract-text', [ImageTranslationController::class, 'extractText'])->name('extract.text');

// Register Routes
Route::get('/register', [RegisterController::class, 'showForm'])->name('register');
Route::post('/register', [RegisterController::class, 'registerUser'])->name('register.user');

// Email Verification Routes
Route::get('/email/verify/{id}', [VerificationController::class, 'verify'])->name('verification.verify');
Route::get('/email/verify', function () {
    return view('auth.verify-email');
})->middleware('auth')->name('verification.notice');

// Password Reset Routes
Route::get('/forgot-password', function () {
    return view('auth.forgot-password');
})->middleware('guest')->name('password.request');

Route::post('/forgot-password', function (Request $request) {
    $request->validate(['email' => 'required|email']);
 
    $status = Password::sendResetLink(
        $request->only('email')
    );
 
    return $status === Password::RESET_LINK_SENT
        ? back()->with(['status' => __($status)])
        : back()->withErrors(['email' => __($status)]);
})->middleware('guest')->name('password.email');

Route::get('/reset-password/{token}', [VerificationController::class, 'showResetForm'])
    ->middleware('guest')
    ->name('password.reset');

Route::post('/reset-password', [VerificationController::class, 'resetPassword'])
    ->middleware('guest')
    ->name('password.update');

// Authentication Routes
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Admin routes (outside auth middleware)
Route::prefix('admin')->group(function () {
    Route::get('/login', [AdminController::class, 'login'])->name('admin.login');
    Route::post('/login', [AdminController::class, 'authenticate'])->name('admin.authenticate');
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    Route::post('/logout', [AdminController::class, 'logout'])->name('admin.logout');
});

// All routes that require authentication
Route::middleware(['auth', 'verified'])->group(function () {
    // Base navigation views
    Route::get('/text', fn () => view('text'))->name('text');
    Route::get('/pdf', fn () => view('pdf'))->name('pdf');

    // Video Translation
    Route::post('/translate-video', [VideoTranslateController::class, 'translate'])->name('translate.video');

    // Text Translation
    Route::get('/text-translate', [TextTranslateController::class, 'index'])->name('text.translate');
    Route::post('/translate-text', [TextTranslateController::class, 'translate'])->name('translate.text');

    // PDF Translation
    Route::post('/translate-pdf', [PdfTranslateController::class, 'translate'])->name('translate.pdf');

    // PDF to Word
    Route::get('/pdftoword', [PdfToWordController::class, 'index'])->name('pdftoword');
    Route::post('/convert-pdftoword', [PdfToWordController::class, 'convert'])->name('convert.pdftoword');
    Route::get('/download-word/{filename}', [PdfToWordController::class, 'download'])->name('download.word');

    // Merge PDF
    Route::prefix('mergepdf')->name('mergepdf.')->group(function () {
        Route::get('/', [MergePdfController::class, 'index'])->name('index');
        Route::post('/convert', [MergePdfController::class, 'convert'])->name('convert');
        Route::get('/download/{filename}', [MergePdfController::class, 'download'])->name('download');
    });

    // Audio to Text
    Route::get('/audiototext', [AudioToTextController::class, 'index'])->name('audiototext');
    Route::post('/audiototext/convert', [AudioToTextController::class, 'convert'])->name('audiototext.convert');

    // Text Summarizer
    Route::get('/textsummary', [TextSummaryController::class, 'index'])->name('textsummary');
    Route::post('/summarize-text', [TextSummaryController::class, 'summarize'])->name('summarize.text');

    // Speech to Text
    Route::get('/speechtotext', [SpeechToTextController::class, 'index'])->name('speechtotext');

    // Plagiarism Checker
    Route::get('/plagiarism', [PlagiarismController::class, 'index'])->name('plagiarism');
    Route::post('/check-plagiarism', [PlagiarismController::class, 'check'])->name('check.plagiarism');

    // Image to Ghibli
    Route::get('/imagetoghibli', [ImageToGhibliController::class, 'index'])->name('imagetoghibli');
    Route::post('/convert-imagetoghibli', [ImageToGhibliController::class, 'convert'])->name('convert.imagetoghibli');

    // Audio Transcription
    Route::get('/audio', [AudioTranscriptionController::class, 'index'])->name('audio.index');
    Route::post('/audio/transcribe', [AudioTranscriptionController::class, 'transcribe'])->name('audio.transcribe');

    // QR Code routes
    Route::get('/qr', [QRController::class, 'index'])->name('qr.index');
    Route::post('/qr/generate', [QRController::class, 'generate'])->name('qr.generate');

    // Feedback routes
    Route::get('/feedback', [FeedbackController::class, 'index'])->name('feedback');
    Route::post('/feedback', [FeedbackController::class, 'store'])->name('feedback.store');

    // History page
    Route::get('/history', function () {
        return view('history');
    })->name('history');

    // Test email route
    Route::get('/test-email', function () {
        try {
            $user = auth()->user();
            if (!$user) {
                return 'No authenticated user found';
            }

            $emailData = [
                'user' => $user,
                'message' => "Test email message",
                'timestamp' => now()->format('Y-m-d H:i:s')
            ];

            \Illuminate\Support\Facades\Mail::to($user->email)->send(new \App\Mail\MotivationEmail($emailData));
            
            return 'Email sent successfully to: ' . $user->email;
        } catch (\Exception $e) {
            return 'Error: ' . $e->getMessage();
        }
    });

    // Images to PDF (change to singular)
    Route::get('/imagetopdf', [ImagesToPdfController::class, 'index'])->name('imagetopdf');
    Route::post('/convert-imagestopdf', [ImagesToPdfController::class, 'convert'])->name('convert.imagestopdf');
});

// Redirect /imagestopdf (plural) to /imagetopdf (singular)
Route::get('/imagestopdf', function() {
    return redirect('/imagetopdf');
});
