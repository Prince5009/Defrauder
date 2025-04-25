@extends('layout.app')

@section('title', 'Merge PDF Files')

@section('content')
<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card bg-warning bg-opacity-25">
                <div class="card-header bg-warning bg-opacity-50">
                    <h4 class="mb-0">Merge PDF Files</h4>
                </div>

                <div class="card-body">
                    @if(session('message'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('message') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <form action="{{ route('mergepdf.convert') }}" method="POST" enctype="multipart/form-data" id="mergeForm">
                        @csrf
                        <div class="mb-3">
                            <label for="pdf_file1" class="form-label">First PDF File</label>
                            <input type="file" 
                                   class="form-control @error('pdf_file1') is-invalid @enderror" 
                                   id="pdf_file1" 
                                   name="pdf_file1" 
                                   accept=".pdf"
                                   required>
                            @error('pdf_file1')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="pdf_file2" class="form-label">Second PDF File</label>
                            <input type="file" 
                                   class="form-control @error('pdf_file2') is-invalid @enderror" 
                                   id="pdf_file2" 
                                   name="pdf_file2" 
                                   accept=".pdf"
                                   required>
                            @error('pdf_file2')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="form-text mb-3">
                            <ul class="mb-0">
                                <li>Both files must be PDF format</li>
                                <li>Maximum size per file: 10MB</li>
                            </ul>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary" id="submitBtn">
                                <i class="fas fa-file-pdf me-2"></i>Merge PDFs
                            </button>
                        </div>
                    </form>

                    @if(session('merged_file'))
                        <div class="mt-4">
                            <div class="card">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <i class="fas fa-file-pdf fa-2x text-danger me-2"></i>
                                            <span class="h5 mb-0">Merged PDF</span>
                                        </div>
                                        <a href="{{ route('mergepdf.download', ['filename' => session('merged_file')]) }}" 
                                           class="btn btn-success">
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
document.getElementById('mergeForm').addEventListener('submit', function(e) {
    const file1 = document.getElementById('pdf_file1').files[0];
    const file2 = document.getElementById('pdf_file2').files[0];
    const maxSize = 10 * 1024 * 1024; // 10MB in bytes
    const submitBtn = document.getElementById('submitBtn');
    
    if (!file1 || !file2) {
        e.preventDefault();
        alert('Please select both PDF files.');
        return;
    }
    
    if (!file1.type.match('application/pdf') || !file2.type.match('application/pdf')) {
        e.preventDefault();
        alert('Both files must be in PDF format.');
        return;
    }
    
    if (file1.size > maxSize || file2.size > maxSize) {
        e.preventDefault();
        alert('Each file must be smaller than 10MB.');
        return;
    }
    
    // Disable submit button and show loading state
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Merging...';
});

// Re-enable submit button when user changes files
['pdf_file1', 'pdf_file2'].forEach(function(id) {
    document.getElementById(id).addEventListener('change', function() {
        const submitBtn = document.getElementById('submitBtn');
        submitBtn.disabled = false;
        submitBtn.innerHTML = '<i class="fas fa-file-pdf me-2"></i>Merge PDFs';
    });
});
</script>
@endsection
