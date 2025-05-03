from django.views.decorators.csrf import csrf_exempt
from django.http import HttpResponse
import qrcode
import io

@csrf_exempt
def generate_qr(request):
    if request.method == 'POST':
        link = request.POST.get('link')
        if not link:
            return HttpResponse('Link is required.', status=400)

        qr = qrcode.make(link)
        buffer = io.BytesIO()
        qr.save(buffer, format='PNG')
        buffer.seek(0)

        return HttpResponse(buffer, content_type='image/png')
    else:
        return HttpResponse('Only POST method allowed.', status=405)
