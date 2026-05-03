@extends('layouts.admin', ['heading' => 'Media Library', 'subheading' => 'Upload images and files and reuse them across pages and posts.'])

@section('content')
    <div class="grid-2">
        <section class="card">
            <h2 style="margin-top:0;">Upload File</h2>
            <form method="post" action="{{ route('admin.media.store') }}" enctype="multipart/form-data">
                @csrf
                <div class="field"><label>File</label><input type="file" name="file" required></div>
                <div class="field"><label>Alt Text</label><input name="alt_text"></div>
                <button type="submit">Upload</button>
            </form>
        </section>
        <section class="card">
            <h2 style="margin-top:0;">Library</h2>
            <div class="grid-3">
                @foreach($media as $item)
                    <article class="card" style="padding:0.9rem;">
                        <strong>{{ $item->filename }}</strong>
                        <div class="muted">{{ $item->mime_type }}</div>
                        <div class="muted">#{{ $item->id }}</div>
                        <form method="post" action="{{ route('admin.media.destroy', $item) }}" style="margin-top:.8rem;">@csrf @method('delete')<button class="danger">Delete</button></form>
                    </article>
                @endforeach
            </div>
            <div style="margin-top:1rem;">{{ $media->links() }}</div>
        </section>
    </div>
@endsection
