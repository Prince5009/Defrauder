@extends('layout.app')
@section('title', 'PDF to Word')

@section('content')
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-success text-white">
                    <h2 class="mb-0">PDF to Word Converter</h2>
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

                    <form action="{{ route('convert.pdftoword') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-3">
                            <label for="pdf_file" class="form-label">Select PDF File</label>
                            <input type="file" class="form-control" id="pdf_file" name="pdf_file" accept=".pdf" required>
                        </div>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-success btn-lg">Convert to Word</button>
                        </div>
                    </form>

                    @if(session('word_file'))
                        <div class="mt-4">
                            <div class="card">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <i class="fas fa-file-word fa-2x text-primary me-2"></i>
                                            <span class="h5 mb-0">{{ session('word_file') }}</span>
                                        </div>
                                        <a href="{{ route('download.word', ['filename' => session('word_file')]) }}" 
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
@endsection
