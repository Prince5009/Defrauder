import os
import tempfile
import av
from django.http import JsonResponse, FileResponse, HttpResponse
from rest_framework.views import APIView
from rest_framework.parsers import MultiPartParser
from rest_framework.response import Response
from rest_framework import status
import logging

logger = logging.getLogger(__name__)

# Create a permanent directory for audio files
BASE_DIR = os.path.dirname(os.path.dirname(os.path.abspath(__file__)))
AUDIO_FILES_DIR = os.path.join(BASE_DIR, 'media', 'audio_files')
os.makedirs(AUDIO_FILES_DIR, exist_ok=True)

class ExtractAudioAPIView(APIView):
    parser_classes = [MultiPartParser]

    def post(self, request, *args, **kwargs):
        video_file = request.FILES.get('video')

        if not video_file:
            return Response({'error': 'No video file provided'}, status=400)

        try:
            # Save video temporarily
            with tempfile.NamedTemporaryFile(delete=False, suffix='.mp4') as temp_video:
                for chunk in video_file.chunks():
                    temp_video.write(chunk)
                temp_video_path = temp_video.name

            # Generate unique filename for the output audio
            output_filename = f'audio_{os.path.splitext(video_file.name)[0]}.wav'
            output_audio_path = os.path.join(AUDIO_FILES_DIR, output_filename)

            logger.info(f"Processing video file: {video_file.name}")
            logger.info(f"Output audio path: {output_audio_path}")

            # Extract audio using PyAV
            input_container = av.open(temp_video_path)
            output_container = av.open(output_audio_path, mode='w', format='wav')
            audio_stream = output_container.add_stream('pcm_s16le')

            for frame in input_container.decode(audio=0):
                frame.pts = None
                packet = audio_stream.encode(frame)
                if packet:
                    output_container.mux(packet)

            # Flush encoder
            packet = audio_stream.encode(None)
            if packet:
                output_container.mux(packet)

            input_container.close()
            output_container.close()

            logger.info(f"Audio extraction completed. File saved at: {output_audio_path}")

            # Verify file exists and is readable
            if not os.path.exists(output_audio_path):
                raise Exception("Audio file was not created successfully")

            # Return JSON response with file information
            return JsonResponse({
                'success': True,
                'message': 'Audio extracted successfully',
                'audio_file': output_filename
            })

        except Exception as e:
            logger.error(f"Error during extraction: {str(e)}")
            return Response({'error': str(e)}, status=500)
        finally:
            # Clean up temporary video file
            if os.path.exists(temp_video_path):
                os.remove(temp_video_path)

    def get(self, request, filename=None, *args, **kwargs):
        """Handle audio file download"""
        if not filename:
            return Response({'error': 'No filename provided'}, status=400)

        try:
            audio_path = os.path.join(AUDIO_FILES_DIR, filename)
            logger.info(f"Attempting to download file: {audio_path}")

            if not os.path.exists(audio_path):
                logger.error(f"File not found: {audio_path}")
                return Response({'error': 'Audio file not found'}, status=404)

            if not os.access(audio_path, os.R_OK):
                logger.error(f"File not readable: {audio_path}")
                return Response({'error': 'File not accessible'}, status=403)

            # Open and read the file
            try:
                with open(audio_path, 'rb') as audio_file:
                    response = HttpResponse(audio_file.read(), content_type='audio/wav')
                    response['Content-Disposition'] = f'attachment; filename="{filename}"'
                    logger.info(f"File download successful: {filename}")
                    return response
            except IOError as e:
                logger.error(f"Error reading file: {str(e)}")
                return Response({'error': f'Error reading file: {str(e)}'}, status=500)

        except Exception as e:
            logger.error(f"Error during download: {str(e)}")
            return Response({'error': str(e)}, status=500)
