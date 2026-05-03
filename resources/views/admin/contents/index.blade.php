@extends('layouts.admin', ['heading' => ucfirst($type).'s', 'subheading' => 'Manage drafts, publishing, scheduling, SEO metadata, and bulk actions.'])

@section('content')
    <div class="card" style="margin-bottom:1rem;">
        <form method="get" class="grid-3">
            <div class="field"><label>Content Type</label><select name="type">@foreach(['page','post','news','custom'] as $option)<option value="{{ $option }}" @selected($type===$option)>{{ ucfirst($option) }}</option>@endforeach</select></div>
            <div class="field"><label>Status</label><select name="status"><option value="">Any</option>@foreach(['draft','published','scheduled','private'] as $status)<option value="{{ $status }}" @selected(request('status')===$status)>{{ ucfirst($status) }}</option>@endforeach</select></div>
            <div class="field" style="align-self:end;"><button type="submit" class="secondary">Filter</button> <a class="btn" href="{{ route('admin.contents.create', ['type' => $type]) }}">New {{ $type }}</a></div>
        </form>
    </div>

    <form method="post" action="{{ route('admin.contents.bulk') }}" class="card table-wrap">
        @csrf
        <input type="hidden" name="type" value="{{ $type }}">
        <table>
            <thead><tr><th></th><th>Title</th><th>Status</th><th>Author</th><th>Taxonomies</th><th>Updated</th><th>Actions</th></tr></thead>
            <tbody>
            @foreach($contents as $content)
                <tr>
                    <td><input type="checkbox" name="content_ids[]" value="{{ $content->id }}" style="width:auto;"></td>
                    <td>
                        <strong>{{ $content->title }}</strong>
                        <div class="muted">/{{ $content->slug }}</div>
                    </td>
                    <td><span class="badge">{{ $content->deleted_at ? 'trashed' : $content->status }}</span></td>
                    <td>{{ $content->author?->name ?? 'Unknown' }}</td>
                    <td>
                        <div class="chip-list">
                            @foreach($content->taxonomies as $taxonomy)
                                <span class="chip">{{ $taxonomy->name }}</span>
                            @endforeach
                        </div>
                    </td>
                    <td>{{ $content->updated_at?->diffForHumans() }}</td>
                    <td class="actions">
                        <a class="btn secondary" href="{{ route('admin.contents.edit', $content) }}">Edit</a>
                        <a class="btn secondary" href="{{ route('admin.builder.edit', $content) }}">Builder</a>
                        @if($content->deleted_at)
                            <form method="post" class="inline" action="{{ route('admin.contents.restore', $content->id) }}">@csrf<button class="secondary">Restore</button></form>
                        @else
                            <form method="post" class="inline" action="{{ route('admin.contents.destroy', $content) }}">@csrf @method('delete')<button class="danger">Trash</button></form>
                        @endif
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
        <div class="actions" style="margin-top:1rem;">
            <select name="action" style="max-width:240px;">
                <option value="publish">Publish</option>
                <option value="draft">Move to Draft</option>
                <option value="delete">Trash</option>
                <option value="restore">Restore</option>
                <option value="category">Assign Category</option>
            </select>
            <select name="taxonomy_id" style="max-width:240px;">
                <option value="">Select category</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                @endforeach
            </select>
            <button type="submit">Run bulk action</button>
        </div>
    </form>
    <div style="margin-top:1rem;">{{ $contents->links() }}</div>
@endsection
