const config = {
    API_BASE_URL: 'http://69.62.77.164:8000',
    ENDPOINTS: {
        // Document Processing
        WORD_TO_PDF: '/word-to-pdf/',
        PDF_TO_WORD: '/pdf-to-word/',
        MERGE_PDF: '/merge-pdf/',
        
        // Text Processing
        TEXT_SUMMARIZER: '/text-summarizer/',
        TEXT_TRANSLATOR: '/text-translator/',
        PLAGIARISM_CHECKER: '/plagiarism-checker/',
        
        // Media Processing
        VIDEO_TRIMMER: '/video-trimmer/',
        SPEECH_TO_TEXT: '/speech-to-text/',
        EXTRACT_AUDIO: '/extract-audio/',
        
        // Image Processing
        IMAGE_TO_PDF: '/image-to-pdf/',
        EXTRACT_TEXT: '/extract-text/',
        QR_GENERATOR: '/qr/generate/'
    }
};

export default config; 