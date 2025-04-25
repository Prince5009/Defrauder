import os
import ffmpeg
from django.http import JsonResponse
from django.core.files.storage import FileSystemStorage
from rest_framework.decorators import api_view
from django.conf import settings

@api_view(['POST'])
def trim_video(request):
    # Ensure a file is uploaded
    if 'video_file' not in request.FILES:
        return JsonResponse({'error': 'No video file uploaded'}, status=400)

    video_file = request.FILES['video_file']
    
    # Save the video to the media directory
    fs = FileSystemStorage(location=os.path.join(settings.MEDIA_ROOT, 'videos'))
    filename = fs.save(video_file.name, video_file)
    file_path = os.path.join(fs.location, filename)

    # Check if the file exists
    if not os.path.exists(file_path):
        return JsonResponse({'error': 'File not found after upload'}, status=404)

    # Get start and end time from request data
    start_time = request.data.get('start_time')
    end_time = request.data.get('end_time')

    if not start_time or not end_time:
        return JsonResponse({'error': 'Start time and end time are required'}, status=400)

    try:
        # Convert start_time and end_time to float
        start_time = float(start_time)
        end_time = float(end_time)

        # Generate the output file path
        output_file = os.path.join(settings.MEDIA_ROOT, 'videos', f"trimmed_{filename}")

        # Use python-ffmpeg to trim the video
        ffmpeg.input(file_path, ss=start_time, to=end_time).output(output_file, c='copy').run()

        # Return the path of the trimmed video
        return JsonResponse({
            'message': 'Video trimmed successfully',
            'output_file': output_file
        })

    except ffmpeg.Error as e:
        return JsonResponse({'error': str(e)}, status=500)
    except Exception as e:
        return JsonResponse({'error': str(e)}, status=500)
