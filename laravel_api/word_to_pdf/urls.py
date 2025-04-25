from django.urls import path
from .views import WordToPDFAPIView

urlpatterns = [
    path('convert/', WordToPDFAPIView.as_view(), name='word-to-pdf'),
]
