# text_summarizer/urls.py
from django.urls import path
from .views import summarize_text

urlpatterns = [
    path('content/', summarize_text),
]
