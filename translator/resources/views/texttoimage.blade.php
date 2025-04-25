@extends('layout.app')

@section('title', 'Text to Image')

@section('content')
<div class="container mt-4">
    <h2 class="text-center mb-4">🎨 Text to Image Generator</h2>
    <form action="{{ route('generate.texttoimage') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label for="prompt" class="form-label">Enter Text Prompt</label>
            <input type="text" name="prompt" class="form-control" placeholder="Describe the image you want...">
        </div>
        <button type="submit" class="btn btn-info">Generate Image</button>
    </form>
</div>
@endsection
