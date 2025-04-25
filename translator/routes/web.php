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

// Authentication Routes
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// All routes that require authentication
Route::middleware(['auth'])->group(function () {
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

    // Word to PDF
    Route::get('/wordtopdf', [WordToPdfController::class, 'index'])->name('wordtopdf');
    Route::post('/convert-wordtopdf', [WordToPdfController::class, 'convert'])->name('convert.wordtopdf');
    Route::get('/download-pdf/{filename}', [WordToPdfController::class, 'download'])->name('download.pdf');

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

    // Images to PDF
    Route::get('/imagestopdf', [ImagesToPdfController::class, 'index'])->name('imagestopdf');
    Route::post('/convert-imagestopdf', [ImagesToPdfController::class, 'convert'])->name('convert.imagestopdf');

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
});
