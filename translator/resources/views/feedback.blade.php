@extends('layout.app')

@section('title', 'Feedback - OneSolution')

@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8 col-md-10">
            <div class="card shadow-sm">
                <div class="card-header bg-danger text-white">
                    <h4 class="mb-0"><i class="bi bi-chat-square-text me-2"></i>Feedback</h4>
                </div>
                <div class="card-body">
                    <form id="feedbackForm">
                        @csrf
                        <div class="mb-4">
                            <label for="suggestion" class="form-label">Your Feedback</label>
                            <textarea class="form-control" id="suggestion" name="suggestion" rows="5" placeholder="Please share your thoughts, suggestions, or any issues you've encountered..." required></textarea>
                        </div>
                        <div class="text-center">
                            <button type="button" class="btn btn-danger" onclick="submitFeedback()">
                                <i class="bi bi-send me-2"></i>Submit Feedback
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Star Rating Modal -->
<div class="modal fade" id="ratingModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body text-center p-4">
                <h4 class="text-danger mb-4">How would you rate your experience?</h4>
                <div class="rating-stars mb-4">
                    <i class="bi bi-star-fill star" data-rating="1" style="font-size: 2rem; color: #ddd; cursor: pointer;"></i>
                    <i class="bi bi-star-fill star" data-rating="2" style="font-size: 2rem; color: #ddd; cursor: pointer;"></i>
                    <i class="bi bi-star-fill star" data-rating="3" style="font-size: 2rem; color: #ddd; cursor: pointer;"></i>
                    <i class="bi bi-star-fill star" data-rating="4" style="font-size: 2rem; color: #ddd; cursor: pointer;"></i>
                    <i class="bi bi-star-fill star" data-rating="5" style="font-size: 2rem; color: #ddd; cursor: pointer;"></i>
                </div>
                <div class="d-flex justify-content-center gap-3">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" onclick="submitRating()">Submit Rating</button>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.star {
    transition: color 0.3s ease;
    margin: 0 5px;
}

.star:hover, .star.active {
    color: #ffc107 !important;
}

.rating-stars {
    display: flex;
    justify-content: center;
    gap: 10px;
}
</style>

<script>
let selectedRating = 0;

function submitFeedback() {
    const suggestion = document.getElementById('suggestion').value;
    if (!suggestion.trim()) {
        alert('Please enter your feedback before submitting.');
        return;
    }
    
    // Show rating modal
    const ratingModal = new bootstrap.Modal(document.getElementById('ratingModal'));
    ratingModal.show();
}

// Handle star rating
document.querySelectorAll('.star').forEach(star => {
    star.addEventListener('click', function() {
        const rating = parseInt(this.getAttribute('data-rating'));
        selectedRating = rating;
        
        // Update star colors
        document.querySelectorAll('.star').forEach(s => {
            const sRating = parseInt(s.getAttribute('data-rating'));
            s.style.color = sRating <= rating ? '#ffc107' : '#ddd';
        });
    });
});

function submitRating() {
    if (selectedRating === 0) {
        alert('Please select a rating before submitting.');
        return;
    }

    const suggestion = document.getElementById('suggestion').value;
    const formData = new FormData();
    formData.append('suggestion', suggestion);
    formData.append('rating', selectedRating);
    formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));

    fetch('/feedback', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Thank you for your feedback!');
            window.location.href = '/';
        } else {
            alert('There was an error submitting your feedback. Please try again.');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('There was an error submitting your feedback. Please try again.');
    });
}
</script>
@endsection 