from rest_framework.views import APIView
from rest_framework.response import Response
from rest_framework.parsers import MultiPartParser
from rest_framework import status
from PyPDF2 import PdfMerger
from django.http import FileResponse, HttpResponse
import io
import logging

logger = logging.getLogger(__name__)

class MergePDFView(APIView):
    parser_classes = [MultiPartParser]

    def post(self, request, *args, **kwargs):
        try:
            # Try both 'pdfs' and 'pdfs[]' as field names
            files = request.FILES.getlist('pdfs') or request.FILES.getlist('pdfs[]')
            
            if not files:
                logger.error("No PDF files received in request")
                return Response(
                    {"error": "No PDF files uploaded."}, 
                    status=status.HTTP_400_BAD_REQUEST
                )

            if len(files) < 2:
                logger.error("Only one PDF file received, minimum 2 required")
                return Response(
                    {"error": "At least 2 PDF files are required."}, 
                    status=status.HTTP_400_BAD_REQUEST
                )

            merger = PdfMerger()

            for file in files:
                if not file.name.lower().endswith('.pdf'):
                    logger.error(f"Invalid file type received: {file.name}")
                    return Response(
                        {"error": f"{file.name} is not a PDF file."}, 
                        status=status.HTTP_400_BAD_REQUEST
                    )
                try:
                    merger.append(file)
                except Exception as e:
                    logger.error(f"Error appending PDF {file.name}: {str(e)}")
                    return Response(
                        {"error": f"Error processing {file.name}: {str(e)}"}, 
                        status=status.HTTP_400_BAD_REQUEST
                    )

            merged_pdf = io.BytesIO()
            try:
                merger.write(merged_pdf)
                merger.close()
                merged_pdf.seek(0)
            except Exception as e:
                logger.error(f"Error writing merged PDF: {str(e)}")
                return Response(
                    {"error": f"Error creating merged PDF: {str(e)}"}, 
                    status=status.HTTP_500_INTERNAL_SERVER_ERROR
                )

            response = HttpResponse(merged_pdf.getvalue(), content_type='application/pdf')
            response['Content-Disposition'] = 'attachment; filename=merged.pdf'
            return response

        except Exception as e:
            logger.error(f"Unexpected error in merge PDF view: {str(e)}")
            return Response(
                {"error": "An unexpected error occurred while merging PDFs."}, 
                status=status.HTTP_500_INTERNAL_SERVER_ERROR
            )
