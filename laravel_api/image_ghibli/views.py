import os
from django.http import JsonResponse, HttpResponse
from rest_framework.decorators import api_view, parser_classes
from rest_framework.parsers import MultiPartParser, FormParser
from PIL import Image
from io import BytesIO

@api_view(['POST'])
@parser_classes([MultiPartParser, FormParser])
def convert_to_ghibli(request):
    image_file = request.FILES.get('image')

    if not image_file:
        return JsonResponse({'error': 'No image uploaded'}, status=400)

    try:
        # 🧪 Load the image
        img = Image.open(image_file)

        # 🎨 TODO: Apply Ghibli-style transformation here
        # For now, just simulate by converting to grayscale (as placeholder)
        ghibli_img = img.convert('L')  # replace with actual transformation

        # 💾 Save to buffer
        buffer = BytesIO()
        ghibli_img.save(buffer, format='PNG')
        buffer.seek(0)

        return HttpResponse(buffer, content_type='image/png')

    except Exception as e:
        return JsonResponse({'error': str(e)}, status=500)
