from django.urls import path
from .views import AudioToTextAPIView

urlpatterns = [
    path('text/', AudioToTextAPIView.as_view(), name='audio_to_text'),
]
