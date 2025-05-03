@extends('layout.app')

@section('content')
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-danger text-white">
                    <h3 class="text-center mb-0">
                        <i class="bi bi-qr-code fs-2 me-2"></i>
                        QR Code Generator
                    </h3>
                </div>
                <div class="card-body">
                    <form id="qrForm" class="mb-4">
                        @csrf
                        <div class="form-group">
                            <label for="link">Enter URL or Text</label>
                            <input type="text" class="form-control" id="link" name="link" placeholder="Enter URL or text" required>
                            <small class="form-text text-muted">Enter any URL or text to generate a QR code</small>
                        </div>
                        <button type="submit" class="btn btn-danger mt-3 w-100">
                            <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                            Generate QR Code
                        </button>
                    </form>
                    
                    <div id="qrResult" class="text-center" style="display: none;">
                        <h4 class="text-danger">Your QR Code</h4>
                        <div class="qr-container p-4 bg-light rounded">
                            <img id="qrImage" src="" alt="QR Code" class="img-fluid shadow">
                        </div>
                        <div class="mt-3">
                            <button id="downloadBtn" class="btn btn-success">
                                <i class="bi bi-download me-2"></i>Download QR Code
                            </button>
                        </div>
                    </div>

                    <div id="errorMessage" class="alert alert-danger mt-3" style="display: none;">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.qr-container {
    display: inline-block;
    margin: 20px auto;
    border: 2px solid #f8f9fa;
}

.qr-container img {
    max-width: 300px;
    height: auto;
}

.card-header i {
    vertical-align: middle;
}

.btn-danger {
    background-color: #dc3545;
    border-color: #dc3545;
    transition: all 0.3s ease;
}

.btn-danger:hover {
    background-color: #c82333;
    border-color: #bd2130;
    transform: translateY(-1px);
}

.btn-success {
    transition: all 0.3s ease;
}

.btn-success:hover {
    transform: translateY(-1px);
}
</style>

<script>
document.getElementById('qrForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const link = document.getElementById('link').value;
    const errorDiv = document.getElementById('errorMessage');
    const qrResult = document.getElementById('qrResult');
    const submitButton = this.querySelector('button[type="submit"]');
    const spinner = submitButton.querySelector('.spinner-border');
    
    // Hide any previous error messages and QR code
    errorDiv.style.display = 'none';
    qrResult.style.display = 'none';
    
    // Show loading state
    submitButton.disabled = true;
    spinner.classList.remove('d-none');
    
    // Create form data
    const formData = new FormData();
    formData.append('link', link);
    formData.append('_token', document.querySelector('input[name="_token"]').value);
    
    fetch('{{ route("qr.generate") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
        },
        body: formData
    })
    .then(async response => {
        const contentType = response.headers.get('content-type');
        
        if (contentType && contentType.includes('application/json')) {
            // If we received JSON, it's probably an error
            const jsonResponse = await response.json();
            if (!response.ok) {
                throw new Error(jsonResponse.details || jsonResponse.error || 'Failed to generate QR code');
            }
            return jsonResponse;
        }
        
        if (!response.ok) {
            throw new Error('Failed to generate QR code');
        }
        
        return response.blob();
    })
    .then(result => {
        if (result instanceof Blob) {
            const imageUrl = URL.createObjectURL(result);
            const qrImage = document.getElementById('qrImage');
            qrImage.src = imageUrl;
            qrResult.style.display = 'block';
            
            // Set up download button
            document.getElementById('downloadBtn').onclick = function() {
                const a = document.createElement('a');
                a.href = imageUrl;
                a.download = 'qr-code.png';
                document.body.appendChild(a);
                a.click();
                document.body.removeChild(a);
            };
        } else {
            throw new Error('Unexpected response format');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        errorDiv.innerHTML = `
            <strong>Error:</strong> ${error.message}<br>
            <small>Please check your input and try again.</small>
        `;
        errorDiv.style.display = 'block';
    })
    .finally(() => {
        // Reset button state
        submitButton.disabled = false;
        spinner.classList.add('d-none');
    });
});
</script>
@endsection 