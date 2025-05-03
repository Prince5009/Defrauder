import './bootstrap';
import 'flowbite';
import { apiService } from './services/apiService';

// Mobile menu toggle
document.addEventListener('DOMContentLoaded', function() {
    const mobileMenuButton = document.querySelector('.mobile-menu-button');
    const mobileMenu = document.getElementById('mobile-menu');
    const menuIconOpen = mobileMenuButton?.querySelector('.block');
    const menuIconClose = mobileMenuButton?.querySelector('.hidden');

    if (mobileMenuButton && mobileMenu) {
        mobileMenuButton.addEventListener('click', () => {
            // Toggle menu visibility
            mobileMenu.classList.toggle('hidden');
            
            // Toggle menu icons
            menuIconOpen?.classList.toggle('hidden');
            menuIconClose?.classList.toggle('hidden');
            
            // Toggle aria-expanded
            const expanded = mobileMenuButton.getAttribute('aria-expanded') === 'true';
            mobileMenuButton.setAttribute('aria-expanded', !expanded);
            
            // Add/remove overflow hidden from body when menu is open/closed
            document.body.style.overflow = !expanded ? 'hidden' : '';
        });

        // Close menu when clicking outside
        document.addEventListener('click', (e) => {
            if (!mobileMenu.contains(e.target) && !mobileMenuButton.contains(e.target)) {
                mobileMenu.classList.add('hidden');
                menuIconOpen?.classList.remove('hidden');
                menuIconClose?.classList.add('hidden');
                mobileMenuButton.setAttribute('aria-expanded', 'false');
                document.body.style.overflow = '';
            }
        });

        // Close menu when pressing Escape key
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && !mobileMenu.classList.contains('hidden')) {
                mobileMenu.classList.add('hidden');
                menuIconOpen?.classList.remove('hidden');
                menuIconClose?.classList.add('hidden');
                mobileMenuButton.setAttribute('aria-expanded', 'false');
                document.body.style.overflow = '';
            }
        });
    }
});

// File upload handling
const fileUpload = document.getElementById('file-upload');
const dropZone = fileUpload?.closest('.border-dashed');

if (fileUpload && dropZone) {
    // Prevent default drag behaviors
    ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
        dropZone.addEventListener(eventName, preventDefaults, false);
    });

    // Highlight drop zone when item is dragged over it
    ['dragenter', 'dragover'].forEach(eventName => {
        dropZone.addEventListener(eventName, highlight, false);
    });

    ['dragleave', 'drop'].forEach(eventName => {
        dropZone.addEventListener(eventName, unhighlight, false);
    });

    // Handle dropped files
    dropZone.addEventListener('drop', handleDrop, false);

    // Handle file selection
    fileUpload.addEventListener('change', handleFiles, false);
}

function preventDefaults(e) {
    e.preventDefault();
    e.stopPropagation();
}

function highlight(e) {
    dropZone.classList.add('border-indigo-500', 'bg-indigo-50');
}

function unhighlight(e) {
    dropZone.classList.remove('border-indigo-500', 'bg-indigo-50');
}

function handleDrop(e) {
    const dt = e.dataTransfer;
    const files = dt.files;
    handleFiles({ target: { files } });
}

async function handleFiles(e) {
    const files = [...e.target.files];
    if (files.length > 0) {
        // Show loading state
        dropZone.innerHTML = `
            <div class="flex items-center justify-center">
                <svg class="animate-spin h-5 w-5 text-indigo-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span class="ml-2 text-indigo-500">Uploading...</span>
            </div>
        `;

        try {
            // Determine file type and call appropriate API
            const file = files[0];
            let response;

            if (file.type === 'application/pdf') {
                response = await apiService.convertPdfToWord(file);
            } else if (file.type === 'application/vnd.openxmlformats-officedocument.wordprocessingml.document') {
                response = await apiService.convertWordToPdf(file);
            } else if (file.type.startsWith('image/')) {
                response = await apiService.extractTextFromImage(file);
            } else if (file.type.startsWith('video/')) {
                response = await apiService.extractAudio(file);
            } else if (file.type.startsWith('audio/')) {
                response = await apiService.speechToText(file);
            }

            // Handle the response
            if (response instanceof Blob) {
                // Create download link for file
                const url = window.URL.createObjectURL(response);
                const a = document.createElement('a');
                a.href = url;
                a.download = `converted_${file.name}`;
                document.body.appendChild(a);
                a.click();
                window.URL.revokeObjectURL(url);
                document.body.removeChild(a);
            }

            // Show success message
            dropZone.innerHTML = `
                <div class="text-center">
                    <svg class="mx-auto h-12 w-12 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    <p class="mt-2 text-sm text-gray-600">Upload complete!</p>
                </div>
            `;
        } catch (error) {
            // Show error message
            dropZone.innerHTML = `
                <div class="text-center">
                    <svg class="mx-auto h-12 w-12 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                    <p class="mt-2 text-sm text-gray-600">Error: ${error.message}</p>
                </div>
            `;
        }
    }
} 