@extends('layout.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">{{ __('Text Summarizer') }}</h5>
                </div>

                <div class="card-body">
                    <form id="summarizeForm">
                        @csrf
                        <div class="form-group mb-3">
                            <label for="text" class="form-label">Enter English Text:</label>
                            <textarea class="form-control" id="text" name="text" rows="6" maxlength="10000" required></textarea>
                            <small class="text-muted">Characters: <span id="charCount">0</span>/10000</small>
                        </div>

                        <div class="form-group mb-3">
                            <label for="percent" class="form-label">Select Compression Percent:</label>
                            <input type="number" class="form-control" id="percent" name="percent" value="50" min="1" max="99" required>
                            <small class="text-muted">Lower percentage = more summarization (e.g. 70 = keep 30% content)</small>
                        </div>

                        <div class="form-group mb-3">
                            <button type="submit" class="btn btn-success w-100" id="summarizeBtn">
                                Summarize Text
                            </button>
                        </div>
                    </form>

                    <!-- Result Section (shown just below the button) -->
                    <div id="resultSection" class="mt-4" style="display: none;">
                        <div class="card border-info">
                            <div class="card-header bg-info text-white">
                                Summary Result
                            </div>
                            <div class="card-body">
                                <p id="summaryResult" style="font-size: 1.05em;" class="mb-0"></p>
                            </div>
                        </div>
                    </div>

                    <!-- Error Section -->
                    <div id="errorSection" class="mt-4" style="display: none;">
                        <div class="alert alert-danger" role="alert">
                            <strong>Error:</strong> <span id="errorMessage"></span>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript for handling form submission -->
<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('summarizeForm');
    const textArea = document.getElementById('text');
    const charCount = document.getElementById('charCount');
    const summarizeBtn = document.getElementById('summarizeBtn');
    const resultSection = document.getElementById('resultSection');
    const summaryResult = document.getElementById('summaryResult');
    const errorSection = document.getElementById('errorSection');
    const errorMessage = document.getElementById('errorMessage');

    // Live character count
    textArea.addEventListener('input', function () {
        charCount.textContent = this.value.length;
    });

    form.addEventListener('submit', async function (e) {
        e.preventDefault();

        // Hide previous result/error
        resultSection.style.display = 'none';
        errorSection.style.display = 'none';

        // Disable button with spinner
        summarizeBtn.disabled = true;
        summarizeBtn.innerHTML = `
            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
            Summarizing...
        `;

        try {
            const response = await fetch('{{ route("summarize.text") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                    'Accept': 'application/json',
                },
                body: JSON.stringify({
                    text: textArea.value,
                    percent: parseInt(document.getElementById('percent').value)
                }),
            });

            const data = await response.json();

            if (response.ok) {
                summaryResult.textContent = data.summary;
                resultSection.style.display = 'block';
                resultSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
            } else {
                errorMessage.textContent = data.error || 'An error occurred during summarization.';
                errorSection.style.display = 'block';
                errorSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        } catch (error) {
            errorMessage.textContent = 'Network error occurred. Please check your connection.';
            errorSection.style.display = 'block';
            errorSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
        } finally {
            summarizeBtn.disabled = false;
            summarizeBtn.innerHTML = 'Summarize Text';
        }
    });
});
</script>
@endsection
