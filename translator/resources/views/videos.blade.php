@extends('layout.app')

@section('title', 'Video Translation')

@section('content')
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-danger text-white">
                    <h2 class="mb-0">Video Translation</h2>
                </div>
                <div class="card-body">
                    <p class="text-center mb-4">Upload a video, and we'll attempt to translate its audio content.</p>

                    <!-- Success/Error Messages -->
                    @if(session('message'))
                        <div class="alert alert-success mb-4">
                            {{ session('message') }}
                        </div>
                    @endif
                    @if(session('error'))
                        <div class="alert alert-danger mb-4">
                            {{ session('error') }}
                        </div>
                    @endif

                    <form action="{{ route('translate.video') }}" method="POST" enctype="multipart/form-data" id="translateVideoForm">
                        @csrf

                        <!-- Source Language Dropdown -->
                        <div class="mb-4">
                            <label for="source_lang" class="form-label">Source Language</label>
                            <select class="form-select" id="source_lang" name="source_lang" required>
                                <option value="">Choose language...</option>
                                <option value="en">English</option>
                                <option value="hi">Hindi</option>
                                <option value="gu">Gujarati</option>
                            </select>
                        </div>

                        <!-- Video Upload Section -->
                        <div class="mb-3">
                            <label for="videoInput" class="form-label">Upload Video (MP4, AVI, MKV)</label>
                            <input type="file" class="form-control" id="videoInput" name="video" accept="video/mp4, video/avi, video/mkv" required onchange="previewVideo(event)">
                            <div class="form-text">Max file size: 50MB.</div>
                            <small class="text-danger" id="error-message"></small>
                            <video id="preview" class="img-fluid mt-3 rounded d-none" controls style="max-height: 300px;"></video>
                        </div>

                        <!-- Target Language Dropdown -->
                        <div class="mb-4">
                            <label for="target_lang" class="form-label">Target Language</label>
                            <select class="form-select" id="target_lang" name="target_lang" required>
                                <option value="">Choose language...</option>
                                <option value="en">English</option>
                                <option value="hi">Hindi</option>
                                <option value="gu">Gujarati</option>
                            </select>
                        </div>

                        <!-- Submit Button -->
                        <div class="d-grid">
                            <button type="submit" class="btn btn-danger btn-lg">Translate Video</button>
                        </div>
                    </form>

                    <!-- Result Section (kept for potential future use/display) -->
                    <div id="result-section" class="mt-4 d-none">
                         <h4 class="text-center mb-3">Translation Result</h4>
                         <div class="alert alert-secondary">
                            <p>Translation results (if any) will appear here based on backend processing.</p>
                             <p id="translatedText"></p>
                         </div>
                     </div>

                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function previewVideo(event) {
        const file = event.target.files[0];
        const previewElement = document.getElementById('preview');
        const errorMessageElement = document.getElementById('error-message');
        const allowedTypes = ["video/mp4", "video/avi", "video/mkv"];
        const maxSizeMB = 50;
        const maxSizeBytes = maxSizeMB * 1024 * 1024;

        errorMessageElement.textContent = '';
        previewElement.classList.add('d-none');
        previewElement.src = ''; // Clear previous preview

        if (file) {
            if (!allowedTypes.includes(file.type)) {
                errorMessageElement.textContent = `Invalid file type! Please upload MP4, AVI, or MKV. (Type detected: ${file.type})`;
                event.target.value = '';
                return;
            }
            if (file.size > maxSizeBytes) {
                errorMessageElement.textContent = `File size too large! Max ${maxSizeMB}MB allowed.`;
                event.target.value = '';
                return;
            }

            const reader = new FileReader();
            reader.onload = function(e) {
                previewElement.src = e.target.result;
                previewElement.classList.remove('d-none');
            };
            reader.readAsDataURL(file);
        }
    }

    // Removed the displayResult JS function as the primary feedback
    // will come from the backend via session message after form submission.
    // The result-section div is kept for potential future dynamic updates.
</script>
@endsection
