# text_translator/urls.py
from django.urls import path
from .views import translate_text

urlpatterns = [
    path('translate/', translate_text),
]
