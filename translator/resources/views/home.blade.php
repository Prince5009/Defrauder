@extends('layout.app')

@section('title', 'Extract Text')

@section('content')
<style>
    .extracted-text {
        white-space: pre-wrap;
        word-wrap: break-word;
        font-size: 16px;
        line-height: 1.5;
        font-family: monospace;
        background-color: #f8f9fa;
        padding: 15px;
        border-radius: 5px;
        border: 1px solid #dee2e6;
    }
    .status-success {
        color: #198754;
        font-size: 14px;
        margin-bottom: 10px;
    }
</style>

<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-dark text-white">
                    <h3 class="mb-0">Extract Text from Image</h3>
                </div>
                <div class="card-body">
                    @if(session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif

                    <form action="{{ route('extract.text') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Upload Image</label>
                            <input type="file" name="image" class="form-control" accept="image/*" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Extract Text</button>
                    </form>

                    @if(session('extractedText'))
                        <div class="mt-4">
                            <h4>Extracted Text:</h4>
                            @if(session('status') === 'success')
                                <div class="status-success">
                                    <i class="fas fa-check-circle"></i> Extraction successful
                                </div>
                            @endif
                            <div class="extracted-text">{{ session('extractedText') }}</div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
