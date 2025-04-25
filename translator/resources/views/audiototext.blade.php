@extends('layout.app')

@section('title', 'Audio to Text - OneSolution')

@section('content')
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h4>Audio to Text Converter</h4>
                </div>
                <div class="card-body">
                    <!-- Warning about processing time -->
                    <div class="alert alert-info mb-3">
                        <i class="fas fa-info-circle"></i>
                        Audio processing may take several minutes depending on the file size.
                    </div>

                    <form id="audioForm">
                        @csrf
                        <div class="mb-3">
                            <label for="audio" class="form-label">Select WAV File</label>
                            <input type="file" class="form-control" id="audio" name="audio" accept=".wav" required>
                        </div>
                        <button type="submit" class="btn btn-primary" id="submitBtn">Convert to Text</button>
                    </form>

                    <!-- Processing indicator -->
                    <div id="processingIndicator" class="mt-3" style="display: none;">
                        <div class="d-flex align-items-center mb-2">
                            <div class="spinner-border text-primary me-2" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <span id="processingText">Processing your audio file... This may take several minutes.</span>
                        </div>
                        <div class="progress">
                            <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: 0%"></div>
                        </div>
                    </div>

                    <div id="result" class="mt-3"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('audioForm');
    const submitBtn = document.getElementById('submitBtn');
    const processingIndicator = document.getElementById('processingIndicator');
    const progressBar = document.querySelector('.progress-bar');
    const processingText = document.getElementById('processingText');
    const resultDiv = document.getElementById('result');

    form.onsubmit = async function(e) {
        e.preventDefault();
        
        // Reset and show processing state
        submitBtn.disabled = true;
        processingIndicator.style.display = 'block';
        resultDiv.innerHTML = '';
        let progress = 0;

        // Start progress animation
        const progressInterval = setInterval(() => {
            if (progress < 95) {
                progress += 5;
                progressBar.style.width = progress + '%';
                
                // Update processing message based on progress
                if (progress > 80) {
                    processingText.textContent = 'Almost there... Finalizing the transcription.';
                } else if (progress > 60) {
                    processingText.textContent = 'Still processing... This might take a few more minutes.';
                } else if (progress > 30) {
                    processingText.textContent = 'Converting audio to text... Please wait.';
                }
            }
        }, 3000); // Update every 3 seconds

        const formData = new FormData(this);

        try {
            const response = await fetch('/audiototext/convert', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            });

            clearInterval(progressInterval);
            progressBar.style.width = '100%';

            const data = await response.json();

            if (response.ok && data.transcript) {
                resultDiv.innerHTML = `
                    <div class="alert alert-success">
                        <strong>Transcription Complete!</strong><br>
                        <div class="mt-2">${data.transcript}</div>
                    </div>`;
            } else {
                let errorMessage = data.error || 'An error occurred during transcription.';
                resultDiv.innerHTML = `
                    <div class="alert alert-danger">
                        <strong>Error:</strong> ${errorMessage}
                    </div>`;
            }
        } catch (error) {
            clearInterval(progressInterval);
            resultDiv.innerHTML = `
                <div class="alert alert-danger">
                    <strong>Error:</strong> ${error.message}
                </div>`;
        } finally {
            submitBtn.disabled = false;
            processingIndicator.style.display = 'none';
        }
    };
});
</script>

<style>
.progress {
    height: 4px;
    margin-top: 10px;
}

.spinner-border {
    width: 1.5rem;
    height: 1.5rem;
}
</style>
@endsection