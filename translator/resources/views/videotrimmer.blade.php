@extends('layout.app')
@section('title', 'Video Trimmer')

@section('content')
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-danger text-white">
                    <h2 class="mb-0">Video Trimmer</h2>
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

                    <form action="{{ route('trim.video') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-3">
                            <label for="video_file" class="form-label">Select Video File</label>
                            <input type="file" class="form-control" id="video_file" name="video_file" accept="video/*" required>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="start_time" class="form-label">Start Time (seconds)</label>
                                <input type="number" class="form-control" id="start_time" name="start_time" min="0" step="0.1" placeholder="e.g., 10.5" required>
                            </div>
                            <div class="col-md-6">
                                <label for="end_time" class="form-label">End Time (seconds)</label>
                                <input type="number" class="form-control" id="end_time" name="end_time" min="0" step="0.1" placeholder="e.g., 30.0" required>
                            </div>
                            <div class="form-text mt-1">Enter the start and end times in seconds (e.g., 10.5 for 10 and a half seconds).</div>
                        </div>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-danger btn-lg">Trim Video</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
