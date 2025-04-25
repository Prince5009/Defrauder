@extends('layout.app')

@section('title', 'Text Translation')

@section('content')
<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">Text Translation</h4>
                </div>

                <div class="card-body">
                    <div class="alert alert-info mb-4">
                        <i class="fas fa-info-circle me-2"></i>
                        Enter your English text below and select the target language for translation.
                    </div>

                    <form id="translateForm" action="{{ route('translate.text') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="text" class="form-label">English Text</label>
                            <textarea 
                                class="form-control @error('text') is-invalid @enderror" 
                                id="text" 
                                name="text" 
                                rows="5" 
                                placeholder="Type or paste your English text here (max 1000 characters)..."
                                maxlength="1000"
                                required>{{ old('text') }}</textarea>
                            <div class="form-text">
                                <span id="charCount">0</span>/1000 characters
                            </div>
                            @error('text')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="language" class="form-label">Translate to</label>
                            <select class="form-select @error('language') is-invalid @enderror" 
                                    id="language" 
                                    name="language" 
                                    required>
                                <option value="">Select target language...</option>
                                <option value="hi">Hindi</option>
                                <option value="gu">Gujarati</option>
                            </select>
                            @error('language')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary" id="translateBtn">
                                <i class="fas fa-language me-2"></i>Translate
                            </button>
                        </div>
                    </form>

                    <div id="resultSection" class="mt-4 d-none">
                        <div class="card">
                            <div class="card-header bg-success text-white">
                                <h5 class="mb-0">Translation Result</h5>
                            </div>
                            <div class="card-body">
                                <div class="mb-2">
                                    <strong>Translated to: </strong>
                                    <span id="targetLanguage"></span>
                                </div>
                                <div class="border rounded p-3 bg-light">
                                    <p class="mb-0" id="translatedText" dir="auto"></p>
                                </div>
                                <div class="mt-3 text-end">
                                    <button class="btn btn-sm btn-outline-primary" id="copyBtn">
                                        <i class="fas fa-copy me-1"></i>Copy
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('translateForm');
    const textArea = document.getElementById('text');
    const charCount = document.getElementById('charCount');
    const resultSection = document.getElementById('resultSection');
    const translatedText = document.getElementById('translatedText');
    const targetLanguage = document.getElementById('targetLanguage');
    const languageSelect = document.getElementById('language');
    const translateBtn = document.getElementById('translateBtn');
    const copyBtn = document.getElementById('copyBtn');

    // Initialize character count
    charCount.textContent = textArea.value.length;

    // Update character count
    textArea.addEventListener('input', function() {
        charCount.textContent = this.value.length;
    });

    // Handle form submission
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Remove any existing error messages
        const existingAlerts = document.querySelectorAll('.alert-danger:not([role="alert"])');
        existingAlerts.forEach(alert => alert.remove());
        
        // Show loading state
        translateBtn.disabled = true;
        translateBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Translating...';
        
        // Hide previous results
        resultSection.classList.add('d-none');

        // Get form data
        const formData = {
            text: textArea.value.trim(),
            language: languageSelect.value
        };

        // Send request
        fetch(form.action, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                'Accept': 'application/json'
            },
            body: JSON.stringify(formData)
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            if (data.error) {
                throw new Error(data.error);
            }

            // Show translation result
            translatedText.textContent = data.translatedText;
            targetLanguage.textContent = languageSelect.options[languageSelect.selectedIndex].text;
            resultSection.classList.remove('d-none');

            // Scroll to result
            resultSection.scrollIntoView({ behavior: 'smooth', block: 'center' });
        })
        .catch(error => {
            // Show error message
            const alertDiv = document.createElement('div');
            alertDiv.className = 'alert alert-danger mt-3';
            alertDiv.innerHTML = error.message || 'An error occurred during translation. Please try again.';
            form.insertAdjacentElement('beforebegin', alertDiv);

            // Scroll to error message
            alertDiv.scrollIntoView({ behavior: 'smooth', block: 'center' });
        })
        .finally(() => {
            // Reset button state
            translateBtn.disabled = false;
            translateBtn.innerHTML = '<i class="fas fa-language me-2"></i>Translate';
        });
    });

    // Handle copy button
    copyBtn.addEventListener('click', function() {
        navigator.clipboard.writeText(translatedText.textContent)
            .then(() => {
                const originalText = this.innerHTML;
                this.innerHTML = '<i class="fas fa-check me-1"></i>Copied!';
                this.classList.replace('btn-outline-primary', 'btn-success');
                
                setTimeout(() => {
                    this.innerHTML = originalText;
                    this.classList.replace('btn-success', 'btn-outline-primary');
                }, 2000);
            })
            .catch(() => {
                alert('Failed to copy text. Please try again.');
            });
    });
});
</script>
@endsection 