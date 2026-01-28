@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Create Post</h1>
    <form>
        <div class="form-group">
            <label>Content</label>
            <textarea class="form-control" name="content" required></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Post</button>
    </form>
</div>
@endsection
