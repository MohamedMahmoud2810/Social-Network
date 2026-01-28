@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Trending Posts</h1>
    
    @if($posts->count())
        @foreach($posts as $post)
            <div class="post-card">
                <h3>{{ $post->user->name }}</h3>
                <p>{{ $post->content }}</p>
                @if($post->image_path)
                    <img src="{{ asset('storage/' . $post->image_path) }}" alt="Post image">
                @endif
                <small>{{ $post->created_at->diffForHumans() }}</small>
            </div>
        @endforeach
    @else
        <p>No trending posts yet.</p>
    @endif
</div>
@endsection
