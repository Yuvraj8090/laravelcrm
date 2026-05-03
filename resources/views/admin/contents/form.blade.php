@extends('layouts.admin', ['heading' => $content->exists ? 'Edit Content' : 'Create Content', 'subheading' => 'Pages, posts, custom post types, SEO fields, hierarchy, and media integration.'])

@section('content')
    <form method="post" action="{{ $content->exists ? route('admin.contents.update', $content) : route('admin.contents.store') }}" class="stack">
        @csrf
        @if($content->exists) @method('put') @endif

        <div class="card">
            <div class="grid-2">
                <div class="field"><label>Type</label><input name="type" value="{{ old('type', $content->type ?: 'page') }}"></div>
                <div class="field"><label>Status</label><select name="status">@foreach(['draft','published','scheduled','private'] as $status)<option value="{{ $status }}" @selected(old('status', $content->status ?: 'draft') === $status)>{{ ucfirst($status) }}</option>@endforeach</select></div>
                <div class="field"><label>Title</label><input name="title" value="{{ old('title', $content->title) }}"></div>
                <div class="field"><label>Slug</label><input name="slug" value="{{ old('slug', $content->slug) }}"></div>
                <div class="field"><label>Published At</label><input type="datetime-local" name="published_at" value="{{ old('published_at', optional($content->published_at)->format('Y-m-d\TH:i')) }}"></div>
                <div class="field"><label>Template</label><input name="template" value="{{ old('template', $content->template) }}"></div>
                <div class="field"><label>Parent Page</label><select name="parent_id"><option value="">None</option>@foreach($parents as $parent)<option value="{{ $parent->id }}" @selected((string) old('parent_id', $content->parent_id) === (string) $parent->id)>{{ $parent->title }}</option>@endforeach</select></div>
                <div class="field"><label>Sort Order</label><input type="number" name="sort_order" value="{{ old('sort_order', $content->sort_order ?: 0) }}"></div>
            </div>
            <div class="field"><label>Excerpt</label><textarea name="excerpt">{{ old('excerpt', $content->excerpt) }}</textarea></div>
            <div class="field"><label>Body</label><textarea name="body" style="min-height:280px;">{{ old('body', $content->body) }}</textarea></div>
        </div>

        <div class="grid-2">
            <section class="card">
                <h2 style="margin-top:0;">Categories & Tags</h2>
                <div class="field">
                    <label>Categories</label>
                    <select name="category_ids[]" multiple style="min-height:150px;">
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" @selected(collect(old('category_ids', $content->taxonomies->where('type', 'category')->pluck('id')->all() ?? []))->contains($category->id))>{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="field">
                    <label>Tags</label>
                    <select name="tag_ids[]" multiple style="min-height:150px;">
                        @foreach($tags as $tag)
                            <option value="{{ $tag->id }}" @selected(collect(old('tag_ids', $content->taxonomies->where('type', 'tag')->pluck('id')->all() ?? []))->contains($tag->id))>{{ $tag->name }}</option>
                        @endforeach
                    </select>
                </div>
            </section>

            <section class="card">
                <h2 style="margin-top:0;">Featured Media</h2>
                <div class="field"><label>Featured Media ID</label><input type="number" name="featured_media_id" value="{{ old('featured_media_id', $content->featured_media_id) }}"></div>
                <div class="chip-list">
                    @foreach($mediaLibrary as $media)
                        <span class="chip">#{{ $media->id }} {{ $media->filename }}</span>
                    @endforeach
                </div>
            </section>
        </div>

        <section class="card">
            <h2 style="margin-top:0;">SEO</h2>
            <div class="grid-2">
                <div class="field"><label>Meta Title</label><input name="meta_title" value="{{ old('meta_title', $content->meta_title) }}"></div>
                <div class="field"><label>Meta Keywords</label><input name="meta_keywords" value="{{ old('meta_keywords', $content->meta_keywords) }}"></div>
                <div class="field"><label>OG Title</label><input name="og_title" value="{{ old('og_title', $content->og_title) }}"></div>
                <div class="field"><label>OG Image URL</label><input name="og_image" value="{{ old('og_image', $content->og_image) }}"></div>
            </div>
            <div class="field"><label>Meta Description</label><textarea name="meta_description">{{ old('meta_description', $content->meta_description) }}</textarea></div>
            <div class="field"><label>OG Description</label><textarea name="og_description">{{ old('og_description', $content->og_description) }}</textarea></div>
        </section>

        <div class="actions">
            <button type="submit">{{ $content->exists ? 'Save changes' : 'Create content' }}</button>
            @if($content->exists)
                <a class="btn secondary" href="{{ route('admin.builder.edit', $content) }}">Open builder</a>
            @endif
        </div>
    </form>
@endsection
