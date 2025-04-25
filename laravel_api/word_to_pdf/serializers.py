# pdf_to_word/serializers.py

from rest_framework import serializers

class WordUploadSerializer(serializers.Serializer):
    file = serializers.FileField()
