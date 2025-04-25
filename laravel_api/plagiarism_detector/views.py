# api/views.py
from rest_framework.views import APIView
from rest_framework.response import Response
from rest_framework.parsers import MultiPartParser, FormParser
from PyPDF2 import PdfReader
from sklearn.feature_extraction.text import TfidfVectorizer
from sklearn.metrics.pairwise import cosine_similarity
import logging

logger = logging.getLogger(__name__)

class PlagiarismCheckView(APIView):
    parser_classes = (MultiPartParser, FormParser)

    def post(self, request, format=None):
        try:
            pdf1 = request.FILES.get('pdf1')
            pdf2 = request.FILES.get('pdf2')

            if not pdf1 or not pdf2:
                return Response({'error': 'Both PDF files are required.'}, status=400)

            # Log received files
            logger.info(f"Received files: {pdf1.name}, {pdf2.name}")

            text1 = self.extract_text_from_pdf(pdf1)
            text2 = self.extract_text_from_pdf(pdf2)

            if not text1 or not text2:
                return Response({'error': 'Could not extract text from one or both PDFs.'}, status=400)

            plagiarism_score = self.calculate_similarity(text1, text2)
            
            # Format the response with exactly 2 decimal places
            response_data = {
                'plagiarism_percentage': f'{plagiarism_score:.2f}'  # Remove % symbol
            }
            
            logger.info(f"Plagiarism check completed. Score: {response_data['plagiarism_percentage']}")
            return Response(response_data)

        except Exception as e:
            logger.error(f"Error in plagiarism check: {str(e)}")
            return Response({'error': str(e)}, status=500)

    def extract_text_from_pdf(self, pdf_file):
        try:
            reader = PdfReader(pdf_file)
            text = ''
            for page in reader.pages:
                text += page.extract_text() or ''
            return text.strip()
        except Exception as e:
            logger.error(f"Error extracting text from PDF: {str(e)}")
            return ''

    def calculate_similarity(self, text1, text2):
        if not text1.strip() or not text2.strip():
            return 0.0
        
        try:
            vectorizer = TfidfVectorizer().fit_transform([text1, text2])
            vectors = vectorizer.toarray()
            similarity = cosine_similarity([vectors[0]], [vectors[1]])[0][0]
            return similarity * 100  # percentage
        except Exception as e:
            logger.error(f"Error calculating similarity: {str(e)}")
            return 0.0
