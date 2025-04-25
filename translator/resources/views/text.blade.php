@extends('layout.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-danger text-white    ">{{ __('Text Translation') }}</div>

                <div class="card-body">
                    <form id="translationForm">
                        @csrf
                        <div class="form-group mb-3">
                            <label for="text">Enter English Text:</label>
                            <textarea class="form-control" id="text" name="text" rows="4" required></textarea>
                            <small class="text-muted">Characters: <span id="charCount">0</span>/10000</small>
                        </div>

                        <div class="form-group mb-3">
                            <label for="language">Select Language:</label>
                            <select class="form-control" id="language" name="language" required>
                                <option value="">Select language</option>
                                <option value="hi">Hindi</option>
                                <option value="gu">Gujarati</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary" id="translateBtn">
                                Translate Text
                            </button>
                        </div>
                    </form>

                    <!-- Translation Result Section -->
                    <div id="resultSection" class="mt-4" style="display: none;">
                        <div class="card">
                            <div class="card-header bg-success text-white">
                                Translation Result
                            </div>
                            <div class="card-body">
                                <div id="translationResult" style="font-size: 1.1em; direction: auto;"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Error Message Section -->
                    <div id="errorSection" class="mt-4" style="display: none;">
                        <div class="alert alert-danger" role="alert">
                            <span id="errorMessage"></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('translationForm');
    const textArea = document.getElementById('text');
    const charCount = document.getElementById('charCount');
    const translateBtn = document.getElementById('translateBtn');
    const resultSection = document.getElementById('resultSection');
    const translationResult = document.getElementById('translationResult');
    const errorSection = document.getElementById('errorSection');
    const errorMessage = document.getElementById('errorMessage');

    // Character count
    textArea.addEventListener('input', function() {
        charCount.textContent = this.value.length;
    });

    form.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        // Reset previous results and errors
        resultSection.style.display = 'none';
        errorSection.style.display = 'none';
        
        // Show loading state
        translateBtn.disabled = true;
        translateBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Translating...';

        try {
            const response = await fetch('{{ route("translate.text") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    text: textArea.value,
                    language: document.getElementById('language').value
                })
            });

            const data = await response.json();

            if (response.ok) {
                // Show translation result
                translationResult.textContent = data.translatedText;
                resultSection.style.display = 'block';
                
                // Smooth scroll to result
                resultSection.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
            } else {
                // Show error message
                errorMessage.textContent = data.error || 'An error occurred during translation.';
                errorSection.style.display = 'block';
                errorSection.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
            }
        } catch (error) {
            // Show network error
            errorMessage.textContent = 'Network error occurred. Please try again.';
            errorSection.style.display = 'block';
            errorSection.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        } finally {
            // Reset button state
            translateBtn.disabled = false;
            translateBtn.innerHTML = 'Translate Text';
        }
    });
});
</script>
@endsection
