from django.contrib import admin
from django.urls import path, include


urlpatterns = [
    path('admin/', admin.site.urls),
    path('pdf-to-word/', include('pdf_to_word.urls')),
    path('word-to-pdf/', include('word_to_pdf.urls')),
    path('pdf-to-pdf/', include('mergepdf.urls')),
    path('detect/', include('plagiarism_detector.urls')),
    path('text/', include('text_translator.urls')),
    path('text-summarize/', include('text_summarizer.urls')),
    path('image-ghibli/', include('image_ghibli.urls')),
    path('api/', include('extract_word.urls')),
    path('api/audio/', include('extractAudio.urls')),
    path('api/speech/', include('speech_api.urls')),
    path('api/imagepdf/', include('image_pdf.urls')),
    path('video/',include('video_trimmer.urls')),

]