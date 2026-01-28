@extends('layouts.app')

@section('content')
<div class="container">
    <div class="post-detail">
        <h2>{{ $post->user->name }}</h2>
        <p>{{ $post->content }}</p>
        @if($post->image_path)
            <img src="{{ asset('storage/' . $post->image_path) }}" alt="Post image" class="img-fluid">
        @endif
        <small>{{ $post->created_at->diffForHumans() }}</small>
    </div>
</div>
@endsection
