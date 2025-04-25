from transformers import pipeline
from rest_framework.decorators import api_view
from rest_framework.response import Response

# Load the model once globally (efficient!)
summarizer = pipeline("summarization", model="facebook/bart-large-cnn")

@api_view(['POST'])
def summarize_text(request):
    text = request.data.get("text", "")
    percent = request.data.get("percent", 50)

    if not text:
        return Response({"error": "Text field is required"}, status=400)

    try:
        words = len(text.split())
        # Reduce word count based on percent
        min_len = max(30, int(words * (100 - percent) / 100))  # minimum 30 words
        max_len = max(60, int(words * (100 - percent) / 100) + 20)  # upper cap for variation

        summary_output = summarizer(text, min_length=min_len, max_length=max_len, do_sample=False)

        return Response({
            "summary": summary_output[0]['summary_text']
        })

    except Exception as e:
        return Response({"error": str(e)}, status=500)
