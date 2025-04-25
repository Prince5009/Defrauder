@extends('layout.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Audio to Text Conversion</div>

                <div class="card-body">
                    <form id="audioForm" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group mb-3">
                            <label for="file">Select Audio File (WAV format only)</label>
                            <input type="file" class="form-control" id="file" name="file" accept=".wav" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Convert to Text</button>
                    </form>

                    <div id="conversionResult" class="mt-4" style="display: none;">
                        <h4>Converted Text:</h4>
                        <div class="alert alert-success" role="alert">
                            <p id="convertedText"></p>
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
console.log('Script loaded'); // Debug log

document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM loaded'); // Debug log
    
    const form = document.getElementById('audioForm');
    console.log('Form element:', form); // Debug log
    
    if (form) {
        form.addEventListener('submit', function(e) {
            console.log('Form submitted'); // Debug log
            e.preventDefault();
            
            const formData = new FormData(this);
            const submitButton = this.querySelector('button[type="submit"]');
            const conversionResult = document.getElementById('conversionResult');
            const convertedText = document.getElementById('convertedText');
            const errorMessage = document.getElementById('errorMessage');
            
            // Log form data for debugging
            for (let pair of formData.entries()) {
                console.log(pair[0] + ': ' + pair[1]);
            }
            
            // Reset display
            conversionResult.style.display = 'none';
            errorMessage.style.display = 'none';
            
            // Disable submit button and show loading state
            submitButton.disabled = true;
            submitButton.innerHTML = 'Processing...';

            // Get the CSRF token
            const token = document.querySelector('meta[name="csrf-token"]');
            console.log('CSRF token element:', token); // Debug log
            
            if (!token) {
                console.error('CSRF token not found');
                errorMessage.textContent = 'CSRF token not found. Please refresh the page.';
                errorMessage.style.display = 'block';
                submitButton.disabled = false;
                submitButton.innerHTML = 'Convert to Text';
                return;
            }

            console.log('Sending fetch request'); // Debug log
            
            fetch('/convert-audiototext', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': token.content
                },
                credentials: 'same-origin'
            })
            .then(response => {
                console.log('Response received:', response.status); // Debug log
                if (!response.ok) {
                    throw new Error('Network response was not ok: ' + response.status);
                }
                return response.json();
            })
            .then(data => {
                console.log('Data received:', data); // Debug log
                if (data.success) {
                    convertedText.textContent = data.text;
                    conversionResult.style.display = 'block';
                } else {
                    errorMessage.textContent = data.message || 'An error occurred while processing your request.';
                    errorMessage.style.display = 'block';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                errorMessage.textContent = 'An error occurred while processing your request. Please try again.';
                errorMessage.style.display = 'block';
            })
            .finally(() => {
                submitButton.disabled = false;
                submitButton.innerHTML = 'Convert to Text';
            });
        });
    } else {
        console.error('Form element not found');
    }
});
</script>
@endpush 