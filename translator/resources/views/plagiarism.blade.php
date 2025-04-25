@extends('layout.app')

@section('title', 'Plagiarism Detector')

@section('content')
<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">📄 PDF Plagiarism Detector</h4>
                </div>

                <div class="card-body">
                @if(session('email_success'))
    <div class="alert alert-success alert-dismissible fade show mt-2" role="alert">
        {{ session('email_success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <form action="{{ route('check.plagiarism') }}" method="POST" enctype="multipart/form-data" id="plagiarismForm">
                        @csrf
                        <div class="mb-3">
                            <label for="pdf1" class="form-label">First PDF File</label>
                            <input type="file" 
                                   class="form-control @error('pdf1') is-invalid @enderror" 
                                   id="pdf1" 
                                   name="pdf1" 
                                   accept=".pdf"
                                   required>
                            @error('pdf1')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="pdf2" class="form-label">Second PDF File</label>
                            <input type="file" 
                                   class="form-control @error('pdf2') is-invalid @enderror" 
                                   id="pdf2" 
                                   name="pdf2" 
                                   accept=".pdf"
                                   required>
                            @error('pdf2')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-text mb-3">
                            Maximum file size: 10MB per PDF
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary" id="submitBtn">
                                <i class="fas fa-search me-2"></i>Detect Plagiarism
                            </button>
                        </div>
                    </form>

                    @if(session('plagiarism_percentage') !== null)
                        <div class="mt-4">
                            <div class="card">
                                <div class="card-header bg-info text-white">
                                    <h5 class="mb-0">Plagiarism Analysis Results</h5>
                                </div>
                                <div class="card-body">
                                    <div class="text-center">
                                        <h6 class="mb-3">Comparing:</h6>
                                        <p class="mb-2">
                                            <i class="fas fa-file-pdf text-danger"></i> 
                                            {{ session('file1_name') }}
                                        </p>
                                        <p class="mb-3">
                                            <i class="fas fa-file-pdf text-danger"></i> 
                                            {{ session('file2_name') }}
                                        </p>

                                        <h3 class="mb-3">Similarity Score</h3>
                                        <div class="progress mb-3" style="height: 30px;">
                                            <div class="progress-bar {{ session('plagiarism_percentage') > 50 ? 'bg-danger' : 'bg-success' }}" 
                                                 role="progressbar" 
                                                 style="width: {{ session('plagiarism_percentage') }}%;" 
                                                 aria-valuenow="{{ session('plagiarism_percentage') }}" 
                                                 aria-valuemin="0" 
                                                 aria-valuemax="100">
                                                {{ session('plagiarism_percentage') }}%
                                            </div>
                                        </div>
                                        <p class="mb-0">
                                            @if(session('plagiarism_percentage') > 50)
                                                <span class="text-danger">
                                                    <i class="fas fa-exclamation-triangle me-1"></i>
                                                    High similarity detected between the PDFs
                                                </span>
                                            @else
                                                <span class="text-success">
                                                    <i class="fas fa-check-circle me-1"></i>
                                                    Low similarity detected between the PDFs
                                                </span>
                                            @endif
                                        </p>
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
document.getElementById('plagiarismForm').addEventListener('submit', function(e) {
    const pdf1 = document.getElementById('pdf1').files[0];
    const pdf2 = document.getElementById('pdf2').files[0];
    const submitBtn = document.getElementById('submitBtn');
    const maxSize = 10 * 1024 * 1024; // 10MB
    
    if (!pdf1 || !pdf2) {
        e.preventDefault();
        alert('Please select both PDF files.');
        return;
    }
    
    if (!pdf1.type.match('application/pdf') || !pdf2.type.match('application/pdf')) {
        e.preventDefault();
        alert('Both files must be in PDF format.');
        return;
    }
    
    if (pdf1.size > maxSize || pdf2.size > maxSize) {
        e.preventDefault();
        alert('Each PDF file must be smaller than 10MB.');
        return;
    }
    
    // Disable submit button and show loading state
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Analyzing...';
});
</script>
@endsection
