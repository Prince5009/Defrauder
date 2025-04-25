from django.urls import path
from .views import convert_to_ghibli

urlpatterns = [
    path('conversion/', convert_to_ghibli, name='image_ghibli'),
]
