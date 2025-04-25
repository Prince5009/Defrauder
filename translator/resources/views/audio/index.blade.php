@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Audio Transcription</div>

                <div class="card-body">
                    <form id="audioForm" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group mb-3">
                            <label for="audio_file">Select Audio File (WAV format only)</label>
                            <input type="file" class="form-control" id="audio_file" name="audio_file" accept=".wav" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Transcribe Audio</button>
                    </form>

                    <div id="transcriptionResult" class="mt-4" style="display: none;">
                        <h4>Transcription Result:</h4>
                        <div class="alert alert-success" role="alert">
                            <p id="transcriptionText"></p>
                        </div>
                    </div>

                    <div id="errorMessage" class="mt-4 alert alert-danger" style="display: none;" role="alert">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.getElementById('audioForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const submitButton = this.querySelector('button[type="submit"]');
    const transcriptionResult = document.getElementById('transcriptionResult');
    const transcriptionText = document.getElementById('transcriptionText');
    const errorMessage = document.getElementById('errorMessage');
    
    // Reset display
    transcriptionResult.style.display = 'none';
    errorMessage.style.display = 'none';
    
    // Disable submit button and show loading state
    submitButton.disabled = true;
    submitButton.innerHTML = 'Processing...';
    
    fetch('/audio/transcribe', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            transcriptionText.textContent = data.transcription;
            transcriptionResult.style.display = 'block';
        } else {
            errorMessage.textContent = data.message;
            errorMessage.style.display = 'block';
        }
    })
    .catch(error => {
        errorMessage.textContent = 'An error occurred while processing your request.';
        errorMessage.style.display = 'block';
    })
    .finally(() => {
        submitButton.disabled = false;
        submitButton.innerHTML = 'Transcribe Audio';
    });
});
</script>
@endpush 