@extends('layout.app')

@section('title', 'Image to Ghibli')

@section('content')
<div class="container mt-4">
    <h2 class="text-center mb-4">🌸 Image to Ghibli Style Converter</h2>
    <form action="{{ route('convert.imagetoghibli') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="mb-3">
            <label class="form-label">Upload Your Image</label>
            <input type="file" name="image" class="form-control" accept="image/*">
        </div>
        <button type="submit" class="btn btn-success">Convert to Ghibli Style</button>
    </form>
</div>
@if(session('converted_image'))
    <div class="mt-4 text-center">
        <h4>✨ Converted Ghibli Image:</h4>
        <img src="{{ session('converted_image') }}" class="img-fluid mt-3" alt="Converted Image">
        <br>
        <a href="{{ session('converted_image') }}" class="btn btn-primary mt-3" download>Download Image</a>
    </div>
@endif

@endsection
