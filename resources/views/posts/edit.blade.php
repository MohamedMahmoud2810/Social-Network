@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Edit Post</h1>
    <form>
        <div class="form-group">
            <label>Content</label>
            <textarea class="form-control" name="content" required>{{ $post->content }}</textarea>
        </div>
        <button type="submit" class="btn btn-primary">Update</button>
    </form>
</div>
@endsection
