@extends('layouts.admin', ['heading' => 'Websites', 'subheading' => 'Create, update, soft delete, and restore websites.'])

@php($topActions = '<a class="btn" href="'.route('admin.websites.create').'">New website</a>')

@section('content')
    <div class="card table-wrap">
        <table>
            <thead>
            <tr><th>Name</th><th>Domain</th><th>Status</th><th>Theme</th><th>Actions</th></tr>
            </thead>
            <tbody>
            @foreach($websites as $website)
                <tr>
                    <td>{{ $website->name }}</td>
                    <td>{{ $website->primary_domain ?: 'Not set' }}</td>
                    <td><span class="badge">{{ $website->deleted_at ? 'trashed' : $website->status }}</span></td>
                    <td>{{ $website->theme_slug ?: 'starter' }}</td>
                    <td class="actions">
                        <a class="btn secondary" href="{{ route('admin.websites.edit', $website) }}">Edit</a>
                        @if($website->deleted_at)
                            <form method="post" class="inline" action="{{ route('admin.websites.restore', $website->id) }}">@csrf<button class="secondary">Restore</button></form>
                        @else
                            <form method="post" class="inline" action="{{ route('admin.websites.destroy', $website) }}">@csrf @method('delete')<button class="danger">Trash</button></form>
                        @endif
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    <div style="margin-top:1rem;">{{ $websites->links() }}</div>
@endsection
