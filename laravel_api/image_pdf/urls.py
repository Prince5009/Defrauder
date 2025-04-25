from django.urls import path
from .views import ImageToPdfAPIView

urlpatterns = [
    path('pdf/', ImageToPdfAPIView.as_view(), name='image_to_pdf'),
]
