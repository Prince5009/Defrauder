import os
import pythoncom
from rest_framework.views import APIView
from rest_framework.response import Response
from rest_framework import status
from .serializers import WordUploadSerializer
from django.http import FileResponse
from tempfile import NamedTemporaryFile
from docx2pdf import convert
import shutil

class WordToPDFAPIView(APIView):
    def post(self, request, *args, **kwargs):
        serializer = WordUploadSerializer(data=request.data)
        if serializer.is_valid():
            word_file = serializer.validated_data['file']
            original_name, ext = os.path.splitext(word_file.name)

            try:
                with NamedTemporaryFile(suffix=".docx", delete=False, dir=".") as temp_word:
                    temp_word.write(word_file.read())
                    temp_word_path = temp_word.name

                output_dir = os.path.join(os.path.dirname(temp_word_path), "converted_docs")
                os.makedirs(output_dir, exist_ok=True)

                # 🔧 COM Initialization fix
                pythoncom.CoInitialize()
                convert(temp_word_path, output_dir)
                pythoncom.CoUninitialize()

                output_pdf_path = os.path.join(output_dir, f"{original_name}.pdf")

                if not os.path.exists(output_pdf_path):
                    for f in os.listdir(output_dir):
                        if f.endswith(".pdf"):
                            output_pdf_path = os.path.join(output_dir, f)
                            break

                if os.path.exists(output_pdf_path):
                    return FileResponse(open(output_pdf_path, 'rb'), as_attachment=True, filename=f"{original_name}.pdf")
                else:
                    return Response({"error": "Conversion succeeded, but PDF not found."}, status=500)

            except Exception as e:
                return Response({"error": str(e)}, status=500)

        return Response(serializer.errors, status=400)

