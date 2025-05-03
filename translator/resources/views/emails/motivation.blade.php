<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Time to Get Back to Work! - OneSolution</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .container {
            background-color: #f8f9fa;
            border-radius: 10px;
            padding: 30px;
            text-align: center;
        }
        .message {
            font-size: 18px;
            margin: 20px 0;
            color: #dc3545;
        }
        .emoji {
            font-size: 24px;
            margin: 0 5px;
        }
        .button {
            display: inline-block;
            padding: 10px 20px;
            background-color: #dc3545;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 20px;
        }
        .timestamp {
            font-size: 12px;
            color: #6c757d;
            margin-top: 20px;
        }
        .last-active {
            font-size: 14px;
            color: #6c757d;
            margin-top: 10px;
        }
        .brand {
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="brand">
            <img src="{{ asset('images/OneSolution.jpg') }}" alt="OneSolution Logo" style="height: 48px; border-radius: 8px; vertical-align: middle;">
            <span style="font-size: 2rem; font-weight: bold; color: #dc3545; margin-left: 10px; vertical-align: middle;">OneSolution</span>
        </div>
        <h2>Hello {{ $data['user']->name }},</h2>

        <div class="message">
            {{ $data['message'] }}
        </div>

        <div class="last-active">
            Last active: {{ $data['last_active'] ? $data['last_active']->format('Y-m-d H:i:s') : 'Unknown' }}
        </div>

        <a href="{{ url('/') }}" class="button">Get Back to Work</a>

        <div class="timestamp">
            Sent at: {{ $data['timestamp'] }}
        </div>
    </div>
</body>
</html> 