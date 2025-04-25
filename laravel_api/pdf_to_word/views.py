import os
from rest_framework.views import APIView
from rest_framework.response import Response
from rest_framework import status
from .serializers import PDFUploadSerializer
from django.http import FileResponse
from pdf2docx import Converter
from tempfile import NamedTemporaryFile

class PDFToWordAPIView(APIView):
    def post(self, request, *args, **kwargs):
        serializer = PDFUploadSerializer(data=request.data)
        if serializer.is_valid():
            pdf_file = serializer.validated_data['file']
            
            # Extract original filename without extension
            original_filename = os.path.splitext(pdf_file.name)[0]  # e.g., "mern"

            # Save the uploaded PDF to a temporary file
            with NamedTemporaryFile(suffix=".pdf", delete=False) as temp_pdf:
                temp_pdf.write(pdf_file.read())
                temp_pdf_path = temp_pdf.name

            # Prepare temp file for .docx output
            with NamedTemporaryFile(suffix=".docx", delete=False) as temp_docx:
                temp_docx_path = temp_docx.name

            # Convert PDF to Word
            cv = Converter(temp_pdf_path)
            cv.convert(temp_docx_path, start=0, end=None)
            cv.close()

            # Return the generated Word file with the original name
            response = FileResponse(open(temp_docx_path, 'rb'), as_attachment=True, filename=f"{original_filename}.docx")

            # Optionally clean up temporary files
            os.remove(temp_pdf_path)

            return response

        return Response(serializer.errors, status=status.HTTP_400_BAD_REQUEST)
