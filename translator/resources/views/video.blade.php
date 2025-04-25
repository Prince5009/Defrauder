@extends('layout.app')

@section('title', 'Video to Audio Converter')

@section('content')
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-danger text-white">
                    <h2 class="text-center mb-0">Video to Audio Converter</h2>
                </div>
                <div class="card-body">
                    <form id="videoForm" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-4">
                            <label for="video" class="form-label">Select Video File</label>
                            <input type="file" class="form-control" id="video" name="video" accept="video/*" required>
                            <small class="text-muted">Supported formats: MP4, AVI, MOV, WMV</small>
                        </div>
                        <div class="text-center">
                            <button type="submit" class="btn btn-primary px-4" id="convertBtn">
                                Convert to Audio
                            </button>
                            <div class="spinner-border text-primary loading-spinner mt-3" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                        </div>
                    </form>

                    <div id="alertContainer" class="mt-4 hidden">
                        <div class="alert" role="alert"></div>
                    </div>

                    <div id="downloadContainer" class="text-center mt-4 hidden">
                        <a id="downloadBtn" href="#" class="btn btn-success px-4">
                            <i class="bi bi-download me-2"></i>Download Audio
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .hidden {
        display: none;
    }
    .loading-spinner {
        display: none;
        width: 3rem;
        height: 3rem;
    }
    .card {
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    }
    .card-header {
        background-color: #f8f9fa;
        border-bottom: 1px solid rgba(0, 0, 0, 0.125);
    }
</style>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        const form = $('#videoForm');
        const convertBtn = $('#convertBtn');
        const spinner = $('.loading-spinner');
        const alertContainer = $('#alertContainer');
        const alertElement = alertContainer.find('.alert');
        const downloadContainer = $('#downloadContainer');
        const downloadBtn = $('#downloadBtn');

        form.on('submit', function(e) {
            e.preventDefault();
            
            // Reset UI
            alertContainer.addClass('hidden');
            downloadContainer.addClass('hidden');
            convertBtn.prop('disabled', true);
            spinner.show();

            const formData = new FormData(this);

            $.ajax({
                url: '{{ route("video.extract") }}',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    showAlert('success', 'Video successfully converted to audio!');
                    downloadBtn.attr('href', '{{ url("audio/download") }}/' + response.audio_file);
                    downloadContainer.removeClass('hidden');
                },
                error: function(xhr) {
                    let message = 'An error occurred while converting the video.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        message = xhr.responseJSON.message;
                    }
                    showAlert('danger', message);
                },
                complete: function() {
                    convertBtn.prop('disabled', false);
                    spinner.hide();
                }
            });
        });

        function showAlert(type, message) {
            alertElement
                .removeClass('alert-success alert-danger')
                .addClass('alert-' + type)
                .text(message);
            alertContainer.removeClass('hidden');
        }
    });
</script>
@endsection 