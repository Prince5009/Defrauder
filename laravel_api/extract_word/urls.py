from django.urls import path
from .views import ExtractWordAPI

urlpatterns = [
    path('extract-word/', ExtractWordAPI.as_view(), name='extract-word'),
]
