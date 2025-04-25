from rest_framework.views import APIView
from rest_framework.response import Response
from rest_framework.parsers import MultiPartParser, FormParser
from rest_framework import status
from django.http import FileResponse
from PIL import Image
import io
import uuid

class ImageToPdfAPIView(APIView):
    parser_classes = [MultiPartParser, FormParser]

    def post(self, request):
        try:
            n = int(request.data.get("number_of_images", 0))
            if n <= 0 or n > 10:
                return Response({'error': 'Provide a valid number between 1 and 10.'}, status=400)

            images = []
            for i in range(1, n+1):
                file_key = f'image{i}'
                if file_key not in request.FILES:
                    return Response({'error': f'Missing {file_key}'}, status=400)

                img = Image.open(request.FILES[file_key])
                if img.mode in ("RGBA", "P"):
                    img = img.convert("RGB")
                images.append(img)

            pdf_buffer = io.BytesIO()
            images[0].save(pdf_buffer, format='PDF', save_all=True, append_images=images[1:])
            pdf_buffer.seek(0)

            filename = f"converted_{uuid.uuid4().hex}.pdf"
            return FileResponse(pdf_buffer, as_attachment=True, filename=filename)

        except Exception as e:
            return Response({'error': str(e)}, status=500)
