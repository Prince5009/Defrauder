from django.urls import path
from .views import PDFToWordAPIView

urlpatterns = [
    path('convert/', PDFToWordAPIView.as_view(), name='pdf-to-word'),
]
