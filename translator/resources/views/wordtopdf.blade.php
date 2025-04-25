@extends('layout.app')
@section('title', 'Word to PDF')

@section('content')
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h2 class="mb-0">Word to PDF Converter</h2>
                </div>
                <div class="card-body">
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

                    <form action="{{ route('convert.wordtopdf') }}" method="POST" enctype="multipart/form-data" id="convertForm">
                        @csrf
                        <div class="mb-3">
                            <label for="word_file" class="form-label">Select Word File</label>
                            <div class="input-group">
                                <input type="file" 
                                       class="form-control" 
                                       id="word_file" 
                                       name="word_file" 
                                       accept=".doc,.docx,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document"
                                       required 
                                       onchange="validateWordFile(event)">
                                <label class="input-group-text" for="word_file">
                                    <i class="fas fa-file-word"></i>
                                </label>
                            </div>
                            <div class="form-text">
                                <i class="fas fa-info-circle me-1"></i>
                                Supported formats: .doc, .docx (Max size: 20MB)
                            </div>
                            <small class="text-danger" id="error-message"></small>
                        </div>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-exchange-alt me-2"></i>Convert to PDF
                            </button>
                        </div>
                    </form>

                    @if(session('pdf_file'))
                        <div class="mt-4">
                            <div class="card">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <i class="fas fa-file-pdf fa-2x text-danger me-2"></i>
                                            <span class="h5 mb-0">{{ session('pdf_file') }}</span>
                                        </div>
                                        <a href="{{ route('download.pdf', ['filename' => session('pdf_file')]) }}" 
                                           class="btn btn-primary">
                                            <i class="fas fa-download me-2"></i>Download
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function validateWordFile(event) {
        const file = event.target.files[0];
        const errorMessageElement = document.getElementById('error-message');
        const allowedExtensions = /(\.doc|\.docx)$/i;
        const maxSizeMB = 20; // Set max file size in MB
        const maxSizeBytes = maxSizeMB * 1024 * 1024;

        errorMessageElement.textContent = ''; // Clear previous errors

        if (file) {
            // Validate file type
            if (!allowedExtensions.exec(file.name)) {
                errorMessageElement.textContent = 'Please select a valid Word document (.doc or .docx)';
                event.target.value = ''; // Clear the input
                return false;
            }

            // Validate file size
            if (file.size > maxSizeBytes) {
                errorMessageElement.textContent = `File size too large! Maximum size is ${maxSizeMB}MB`;
                event.target.value = ''; // Clear the input
                return false;
            }
        }
        return true;
    }
</script>
@endsection
