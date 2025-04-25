@extends('layout.app')

@section('title', 'Speech to Text Converter')

@section('content')
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-danger text-white">
                    <h2 class="text-center mb-0">Speech to Text Converter</h2>
                </div>
                <div class="card-body">
                    <div class="text-center mb-4">
                        <button id="startButton" class="btn btn-lg btn-primary rounded-circle p-4 shadow">
                            <i class="bi bi-mic-fill fs-2"></i>
                        </button>
                        <div class="mt-3">
                            <span id="statusText" class="text-muted">Click microphone to start speaking</span>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label">Select Language</label>
                        <select id="languageSelect" class="form-select">
                            <option value="en-US">English (US)</option>
                            <option value="hi-IN">Hindi</option>
                            <option value="gu-IN">Gujarati</option>
                            <option value="es-ES">Spanish</option>
                            <option value="fr-FR">French</option>
                            <option value="de-DE">German</option>
                            <option value="it-IT">Italian</option>
                            <option value="ja-JP">Japanese</option>
                            <option value="ko-KR">Korean</option>
                            <option value="zh-CN">Chinese (Simplified)</option>
                        </select>
                    </div>

                    <div class="card mb-3">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <span>Transcribed Text</span>
                            <button id="copyButton" class="btn btn-sm btn-outline-primary" disabled>
                                <i class="bi bi-clipboard me-1"></i>Copy
                            </button>
                        </div>
                        <div class="card-body">
                            <div id="transcriptionOutput" class="form-control" style="min-height: 200px; overflow-y: auto;">
                                Your speech will appear here...
                            </div>
                        </div>
                    </div>

                    <div class="text-center">
                        <button id="resetButton" class="btn btn-secondary" disabled>
                            <i class="bi bi-trash me-1"></i>Clear Text
                        </button>
                        <button id="downloadButton" class="btn btn-success ms-2" disabled>
                            <i class="bi bi-download me-1"></i>Download Text
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    #startButton {
        transition: all 0.3s ease;
        width: 80px;
        height: 80px;
    }
    #startButton:hover {
        transform: scale(1.1);
    }
    #startButton.recording {
        background-color: #dc3545;
        border-color: #dc3545;
        animation: pulse 1.5s infinite;
    }
    @keyframes pulse {
        0% {
            box-shadow: 0 0 0 0 rgba(220, 53, 69, 0.4);
        }
        70% {
            box-shadow: 0 0 0 10px rgba(220, 53, 69, 0);
        }
        100% {
            box-shadow: 0 0 0 0 rgba(220, 53, 69, 0);
        }
    }
    #transcriptionOutput {
        white-space: pre-wrap;
        font-size: 1.1rem;
        line-height: 1.5;
        background-color: #f8f9fa;
        border: 1px solid #dee2e6;
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const startButton = document.getElementById('startButton');
    const statusText = document.getElementById('statusText');
    const transcriptionOutput = document.getElementById('transcriptionOutput');
    const languageSelect = document.getElementById('languageSelect');
    const copyButton = document.getElementById('copyButton');
    const resetButton = document.getElementById('resetButton');
    const downloadButton = document.getElementById('downloadButton');

    let recognition = null;
    let isRecording = false;

    if ('webkitSpeechRecognition' in window) {
        recognition = new webkitSpeechRecognition();
        recognition.continuous = true;
        recognition.interimResults = true;

        recognition.onstart = function() {
            isRecording = true;
            startButton.classList.add('recording');
            statusText.textContent = 'Listening...';
            startButton.querySelector('i').classList.replace('bi-mic-fill', 'bi-stop-fill');
            resetButton.disabled = true;
        };

        recognition.onend = function() {
            isRecording = false;
            startButton.classList.remove('recording');
            statusText.textContent = 'Click microphone to start speaking';
            startButton.querySelector('i').classList.replace('bi-stop-fill', 'bi-mic-fill');
            resetButton.disabled = false;
        };

        recognition.onresult = function(event) {
            let interimTranscript = '';
            let finalTranscript = '';

            for (let i = event.resultIndex; i < event.results.length; i++) {
                const transcript = event.results[i][0].transcript;
                if (event.results[i].isFinal) {
                    finalTranscript += transcript + '\n';
                } else {
                    interimTranscript += transcript;
                }
            }

            transcriptionOutput.textContent = finalTranscript + interimTranscript;
            if (finalTranscript) {
                copyButton.disabled = false;
                downloadButton.disabled = false;
                resetButton.disabled = false;
            }
        };

        recognition.onerror = function(event) {
            console.error('Speech recognition error:', event.error);
            statusText.textContent = 'Error: ' + event.error;
            startButton.classList.remove('recording');
            isRecording = false;
        };
    } else {
        startButton.disabled = true;
        statusText.textContent = 'Speech recognition is not supported in this browser.';
    }

    startButton.addEventListener('click', function() {
        if (!recognition) return;

        if (!isRecording) {
            recognition.lang = languageSelect.value;
            recognition.start();
        } else {
            recognition.stop();
        }
    });

    languageSelect.addEventListener('change', function() {
        if (isRecording) {
            recognition.stop();
            recognition.lang = this.value;
            recognition.start();
        }
    });

    copyButton.addEventListener('click', function() {
        navigator.clipboard.writeText(transcriptionOutput.textContent)
            .then(() => {
                const originalText = this.innerHTML;
                this.innerHTML = '<i class="bi bi-check me-1"></i>Copied!';
                setTimeout(() => {
                    this.innerHTML = originalText;
                }, 2000);
            });
    });

    resetButton.addEventListener('click', function() {
        transcriptionOutput.textContent = 'Your speech will appear here...';
        copyButton.disabled = true;
        downloadButton.disabled = true;
        this.disabled = true;
    });

    downloadButton.addEventListener('click', function() {
        const text = transcriptionOutput.textContent;
        const blob = new Blob([text], { type: 'text/plain' });
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = 'speech-to-text.txt';
        document.body.appendChild(a);
        a.click();
        window.URL.revokeObjectURL(url);
        document.body.removeChild(a);
    });
});
</script>
@endsection
