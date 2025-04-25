<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Plagiarism Result</title>
</head>
<body>
    <h2>Hello {{ $data['user']->name }},</h2>

    <p>Here are your plagiarism check results:</p>

    <ul>
        <li><strong>File 1:</strong> {{ $data['file1'] }}</li>
        <li><strong>File 2:</strong> {{ $data['file2'] }}</li>
    </ul>

    <h3>Similarity Score: {{ $data['percentage'] }}%</h3>

    {{-- Progress Bar --}}
    <div style="width: 100%; background-color: #e9ecef; border-radius: 4px; overflow: hidden;">
        <div style="
            width: {{ $data['percentage'] }}%;
            background-color: {{ $data['percentage'] > 50 ? '#dc3545' : '#28a745' }};
            color: white;
            text-align: center;
            padding: 10px 0;
            font-weight: bold;
        ">
            {{ $data['percentage'] }}%
        </div>
    </div>

    <p style="margin-top: 20px;">
        @if($data['percentage'] > 50)
            <span style="color: #dc3545;">⚠ High similarity detected between the files.</span>
        @else
            <span style="color: #28a745;">✔ Low similarity detected. You're good to go!</span>
        @endif
    </p>
    

    <p>Thank you for using our service.</p>
</body>
</html>
