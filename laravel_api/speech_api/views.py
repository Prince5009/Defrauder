from rest_framework.views import APIView
from rest_framework.response import Response
from rest_framework.parsers import MultiPartParser
import speech_recognition as sr
import uuid
import os

class AudioToTextAPIView(APIView):
    parser_classes = [MultiPartParser]

    def post(self, request, *args, **kwargs):
        audio_file = request.FILES.get('file')
        if not audio_file:
            return Response({'error': 'No audio file provided'}, status=400)

        # Ensure file is a wav
        if not audio_file.name.endswith('.wav'):
            return Response({'error': 'Only .wav files are supported'}, status=400)

        # Generate unique file path in current directory (not /tmp)
        file_path = f"{uuid.uuid4()}.wav"

        # Save uploaded file to disk
        with open(file_path, 'wb+') as destination:
            for chunk in audio_file.chunks():
                destination.write(chunk)

        recognizer = sr.Recognizer()

        try:
            with sr.AudioFile(file_path) as source:
                audio_data = recognizer.record(source)
                text = recognizer.recognize_google(audio_data)
        except sr.UnknownValueError:
            text = "Sorry, could not understand the audio."
        except sr.RequestError:
            text = "Speech Recognition API is not available or has issues."
        except Exception as e:
            return Response({'error': str(e)}, status=500)
        finally:
            if os.path.exists(file_path):
                os.remove(file_path)

        return Response({'transcript': text})
