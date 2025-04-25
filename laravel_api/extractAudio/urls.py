from django.urls import path
from .views import ExtractAudioAPIView

urlpatterns = [
    path('extract/', ExtractAudioAPIView.as_view(), name='extract-audio'),
    path('download/<str:filename>', ExtractAudioAPIView.as_view(), name='download-audio'),
]
