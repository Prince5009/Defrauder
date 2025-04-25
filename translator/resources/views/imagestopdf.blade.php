@extends('layout.app')
@section('title', 'Images to PDF')

@section('content')
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h4>Convert Images to PDF</h4>
                </div>
                <div class="card-body">
                    <!-- Number of images input -->
                    <div class="mb-4">
                        <label for="number_of_images" class="form-label">How many images do you want to convert?</label>
                        <input type="number" class="form-control" id="number_of_images" min="1" max="10" value="1">
                    </div>

                    <!-- Dynamic form for image uploads -->
                    <form id="imageForm" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="number_of_images" id="hidden_number">
                        <div id="imageInputs" class="mb-4">
                            <!-- Image inputs will be dynamically added here -->
                        </div>
                        <button type="submit" class="btn btn-primary" id="convertBtn">Generate PDF</button>
                    </form>

                    <!-- Loading indicator -->
                    <div id="loading" class="text-center mt-3" style="display: none;">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-2">Converting images to PDF...</p>
                    </div>

                    <!-- Error message -->
                    <div id="error" class="alert alert-danger mt-3" style="display: none;"></div>

                    <!-- PDF Preview Section -->
                    <div id="pdfPreview" class="mt-4" style="display: none;">
                        <div class="card">
                            <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">PDF Generated Successfully</h5>
                                <button class="btn btn-light btn-sm" id="downloadBtn">
                                    <i class="fas fa-download"></i> Download PDF
                                </button>
                            </div>
                            <div class="card-body">
                                <div class="ratio ratio-16x9">
                                    <iframe id="pdfFrame" class="embed-responsive-item" style="border: 1px solid #dee2e6;"></iframe>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const numberInput = document.getElementById('number_of_images');
    const imageInputs = document.getElementById('imageInputs');
    const hiddenNumber = document.getElementById('hidden_number');
    const form = document.getElementById('imageForm');
    const loading = document.getElementById('loading');
    const error = document.getElementById('error');
    const convertBtn = document.getElementById('convertBtn');
    const pdfPreview = document.getElementById('pdfPreview');
    const pdfFrame = document.getElementById('pdfFrame');
    const downloadBtn = document.getElementById('downloadBtn');

    let pdfBlob = null;
    let pdfUrl = null;

    // Generate image input fields based on number
    function generateImageInputs(number) {
        imageInputs.innerHTML = '';
        hiddenNumber.value = number;

        for (let i = 1; i <= number; i++) {
            const div = document.createElement('div');
            div.className = 'mb-3';
            div.innerHTML = `
                <label for="image${i}" class="form-label">Image ${i}</label>
                <input type="file" class="form-control" id="image${i}" name="image${i}" accept="image/*" required>
            `;
            imageInputs.appendChild(div);
        }
    }

    // Initial generation
    generateImageInputs(1);

    // Update on number change
    numberInput.addEventListener('change', function() {
        generateImageInputs(this.value);
    });

    // Handle form submission
    form.addEventListener('submit', async function(e) {
        e.preventDefault();
        error.style.display = 'none';
        loading.style.display = 'block';
        pdfPreview.style.display = 'none';
        convertBtn.disabled = true;

        try {
            const formData = new FormData(this);
            const response = await fetch('/convert-imagestopdf', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            });

            if (response.ok) {
                // Get the blob from response
                pdfBlob = await response.blob();
                
                // Create object URL for preview
                if (pdfUrl) {
                    URL.revokeObjectURL(pdfUrl);
                }
                pdfUrl = URL.createObjectURL(pdfBlob);
                
                // Show preview
                pdfFrame.src = pdfUrl;
                pdfPreview.style.display = 'block';
                
                // Scroll to preview
                pdfPreview.scrollIntoView({ behavior: 'smooth' });
            } else {
                const data = await response.json();
                throw new Error(data.error || 'Failed to convert images to PDF');
            }
        } catch (err) {
            error.textContent = err.message;
            error.style.display = 'block';
        } finally {
            loading.style.display = 'none';
            convertBtn.disabled = false;
        }
    });

    // Handle download button click
    downloadBtn.addEventListener('click', function() {
        if (pdfBlob) {
            const a = document.createElement('a');
            a.href = pdfUrl;
            a.download = 'converted.pdf';
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
        }
    });

    // Cleanup object URL when leaving page
    window.addEventListener('beforeunload', function() {
        if (pdfUrl) {
            URL.revokeObjectURL(pdfUrl);
        }
    });
});
</script>

<style>
#imageInputs {
    max-height: 400px;
    overflow-y: auto;
}

.spinner-border {
    width: 3rem;
    height: 3rem;
}

#pdfFrame {
    min-height: 500px;
    background: #f8f9fa;
}

#downloadBtn {
    white-space: nowrap;
}

.ratio-16x9 {
    --bs-aspect-ratio: 75%;
}
</style>
@endsection
