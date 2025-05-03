import config from '../config';

class ApiService {
    constructor() {
        this.baseUrl = config.API_BASE_URL;
    }

    async request(endpoint, method = 'GET', data = null, headers = {}) {
        const url = `${this.baseUrl}${endpoint}`;
        
        const defaultHeaders = {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
        };

        // Add authorization header if token exists
        const token = localStorage.getItem('auth_token');
        if (token) {
            defaultHeaders['Authorization'] = `Bearer ${token}`;
        }

        const options = {
            method,
            headers: { ...defaultHeaders, ...headers },
            credentials: 'include', // For handling cookies
        };

        if (data) {
            if (data instanceof FormData) {
                delete options.headers['Content-Type'];
                options.body = data;
            } else {
                options.body = JSON.stringify(data);
            }
        }

        try {
            const response = await fetch(url, options);
            
            if (!response.ok) {
                const errorData = await response.json().catch(() => ({}));
                throw new Error(errorData.message || `HTTP error! status: ${response.status}`);
            }

            // Handle different response types
            const contentType = response.headers.get('content-type');
            if (contentType && contentType.includes('application/json')) {
                return await response.json();
            } else if (contentType && contentType.includes('application/pdf')) {
                return await response.blob();
            } else {
                return await response.text();
            }
        } catch (error) {
            console.error('API Error:', error);
            throw error;
        }
    }

    // Document Processing
    async convertWordToPdf(file) {
        const formData = new FormData();
        formData.append('file', file);
        return this.request(config.ENDPOINTS.WORD_TO_PDF, 'POST', formData);
    }

    async convertPdfToWord(file) {
        const formData = new FormData();
        formData.append('file', file);
        return this.request(config.ENDPOINTS.PDF_TO_WORD, 'POST', formData);
    }

    async mergePdfs(files) {
        const formData = new FormData();
        files.forEach(file => formData.append('files', file));
        return this.request(config.ENDPOINTS.MERGE_PDF, 'POST', formData);
    }

    // Text Processing
    async summarizeText(text) {
        return this.request(config.ENDPOINTS.TEXT_SUMMARIZER, 'POST', { text });
    }

    async translateText(text, targetLang) {
        return this.request(config.ENDPOINTS.TEXT_TRANSLATOR, 'POST', { text, target_lang: targetLang });
    }

    async checkPlagiarism(text) {
        return this.request(config.ENDPOINTS.PLAGIARISM_CHECKER, 'POST', { text });
    }

    // Media Processing
    async trimVideo(file, startTime, endTime) {
        const formData = new FormData();
        formData.append('file', file);
        formData.append('start_time', startTime);
        formData.append('end_time', endTime);
        return this.request(config.ENDPOINTS.VIDEO_TRIMMER, 'POST', formData);
    }

    async speechToText(audioFile) {
        const formData = new FormData();
        formData.append('file', audioFile);
        return this.request(config.ENDPOINTS.SPEECH_TO_TEXT, 'POST', formData);
    }

    async extractAudio(videoFile) {
        const formData = new FormData();
        formData.append('file', videoFile);
        return this.request(config.ENDPOINTS.EXTRACT_AUDIO, 'POST', formData);
    }

    // Image Processing
    async convertImageToPdf(images) {
        const formData = new FormData();
        images.forEach(image => formData.append('images', image));
        return this.request(config.ENDPOINTS.IMAGE_TO_PDF, 'POST', formData);
    }

    async extractTextFromImage(image) {
        const formData = new FormData();
        formData.append('image', image);
        return this.request(config.ENDPOINTS.EXTRACT_TEXT, 'POST', formData);
    }

    async generateQRCode(data) {
        return this.request(config.ENDPOINTS.QR_GENERATOR, 'POST', { data });
    }
}

export const apiService = new ApiService(); 