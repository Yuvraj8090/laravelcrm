@extends('layouts.admin', ['heading' => 'Categories & Tags', 'subheading' => 'Manage categories, tags, and custom taxonomies for content organization.'])

@section('content')
    <div class="grid-2">
        <section class="card">
            <h2 style="margin-top:0;">Add Taxonomy</h2>
            <form method="post" action="{{ route('admin.taxonomies.store') }}">
                @csrf
                <div class="field"><label>Type</label><select name="type">@foreach(['category','tag','custom'] as $type)<option value="{{ $type }}">{{ ucfirst($type) }}</option>@endforeach</select></div>
                <div class="field"><label>Name</label><input name="name"></div>
                <div class="field"><label>Slug</label><input name="slug"></div>
                <div class="field"><label>Description</label><textarea name="description"></textarea></div>
                <button type="submit">Save taxonomy</button>
            </form>
        </section>
        <section class="card table-wrap">
            <table>
                <thead><tr><th>Name</th><th>Type</th><th>Slug</th><th>Actions</th></tr></thead>
                <tbody>
                @foreach($taxonomies as $taxonomy)
                    <tr>
                        <td>{{ $taxonomy->name }}</td>
                        <td>{{ $taxonomy->type }}</td>
                        <td>{{ $taxonomy->slug }}</td>
                        <td class="actions">
                            <form method="post" class="inline" action="{{ route('admin.taxonomies.destroy', $taxonomy) }}">@csrf @method('delete')<button class="danger">Delete</button></form>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            <div style="margin-top:1rem;">{{ $taxonomies->links() }}</div>
        </section>
    </div>
@endsection
