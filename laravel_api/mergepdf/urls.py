from django.urls import path
from .views import MergePDFView

urlpatterns = [
    path('merge/', MergePDFView.as_view(), name='merge-pdf'),
]
