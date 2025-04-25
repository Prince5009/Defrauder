from rest_framework.views import APIView
from rest_framework.response import Response
from rest_framework.parsers import MultiPartParser
from PIL import Image
import pytesseract
import cv2
import numpy as np
import tempfile
import os
import io



# Set the exact path to your tesseract.exe
pytesseract.pytesseract.tesseract_cmd = r"C:\Program Files\Tesseract-OCR\tesseract.exe"

class ExtractWordAPI(APIView):
    parser_classes = [MultiPartParser]

    def preprocess_image(self, pil_image):
        # Convert PIL image to OpenCV image
        cv_image = cv2.cvtColor(np.array(pil_image), cv2.COLOR_RGB2BGR)
        
        # Convert to grayscale
        gray = cv2.cvtColor(cv_image, cv2.COLOR_BGR2GRAY)

        # Apply Gaussian Blur
        blurred = cv2.GaussianBlur(gray, (3, 3), 0)

        # Apply Otsu's Thresholding
        _, thresh = cv2.threshold(blurred, 0, 255, cv2.THRESH_BINARY + cv2.THRESH_OTSU)

        return thresh

    def post(self, request, *args, **kwargs):
        # Print request data for debugging
        print("Request Files:", request.FILES)
        print("Request Data:", request.data)

        if 'image' not in request.FILES:
            return Response({
                'error': 'No image file provided',
                'details': 'Please provide an image file with key "image"'
            }, status=400)

        image_file = request.FILES['image']
        
        # Validate file type
        if not image_file.content_type.startswith('image/'):
            return Response({
                'error': 'Invalid file type',
                'details': f'Received {image_file.content_type}, expected image/*'
            }, status=400)

        try:
            # Open and preprocess image
            pil_image = Image.open(image_file).convert('RGB')
            processed_img = self.preprocess_image(pil_image)

            try:
                # Convert processed image to bytes
                is_success, buffer = cv2.imencode(".png", processed_img)
                if not is_success:
                    return Response({
                        'error': 'Failed to process image',
                        'details': 'Image conversion failed'
                    }, status=500)
                
                # Convert buffer to bytes IO object
                io_buf = io.BytesIO(buffer)
                
                # Extract text directly from the buffer
                text = pytesseract.image_to_string(
                    Image.open(io_buf),
                    lang='eng'
                )
                text = text.strip()  # Remove extra whitespace

                if not text:
                    return Response({
                        'error': 'No text detected',
                        'details': 'The image does not contain any readable text'
                    }, status=400)

                return Response({
                    'extracted_text': text,
                    'status': 'success'
                })

            except pytesseract.TesseractError as e:
                return Response({
                    'error': f'Tesseract error: {str(e)}',
                    'details': 'Make sure Tesseract is properly installed'
                }, status=500)
            
        except Exception as e:
            print("Error processing image:", str(e))  # Debug print
            return Response({
                'error': f'Error processing image: {str(e)}',
                'details': 'Please try with a different image'
            }, status=500)
