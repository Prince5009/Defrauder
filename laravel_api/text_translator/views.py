# text_translator/views.py

from rest_framework.decorators import api_view
from rest_framework.response import Response
from googletrans import Translator

LANGUAGE_CODES = {
    "english": "en",
    "hindi": "hi",
    "gujarati": "gu",
}

@api_view(['POST'])
def translate_text(request):
    input_text = request.data.get("text")
    target_language = request.data.get("language", "").lower()

    if not input_text or not target_language:
        return Response({"error": "Both 'text' and 'language' are required."}, status=400)

    if target_language not in LANGUAGE_CODES:
        return Response({"error": "Unsupported language. Choose from: hindi, english, gujarati."}, status=400)

    translator = Translator()
    translated = translator.translate(input_text, dest=LANGUAGE_CODES[target_language])

    return Response({
        "translated_text": translated.text,
        
    })
