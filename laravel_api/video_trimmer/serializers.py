from rest_framework import serializers

class VideoTrimSerializer(serializers.Serializer):
    video = serializers.FileField()
    start_time = serializers.CharField()  # format: mm:ss
    end_time = serializers.CharField()    # format: mm:ss
