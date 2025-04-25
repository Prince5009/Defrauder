# urls.py
from django.urls import path
from .views import trim_video  # Correct function-based view import

urlpatterns = [
    path('trim-video/', trim_video, name='trim_video'),
]
