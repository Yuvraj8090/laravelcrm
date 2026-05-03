@extends('layouts.admin', ['heading' => 'Plugins', 'subheading' => 'Activate, deactivate, and configure installed plugins with dependency checks.'])

@section('content')
    <div class="grid-3">
        @foreach($plugins as $plugin)
            <article class="card">
                <h2 style="margin-top:0;">{{ $plugin['name'] }}</h2>
                <p class="muted">{{ $plugin['description'] ?? 'No description' }}</p>
                <div class="chip-list" style="margin-bottom:1rem;">
                    <span class="chip">{{ $plugin['slug'] }}</span>
                    <span class="chip">v{{ $plugin['version'] }}</span>
                    @if(($active[$plugin['slug']] ?? false))<span class="badge">Active</span>@else<span class="chip">Inactive</span>@endif
                </div>
                <div class="actions">
                    <a class="btn secondary" href="{{ route('admin.plugins.settings', $plugin['slug']) }}">Settings</a>
                    @if(($active[$plugin['slug']] ?? false))
                        <form method="post" class="inline" action="{{ route('admin.plugins.deactivate', $plugin['slug']) }}">@csrf<button class="danger">Deactivate</button></form>
                    @else
                        <form method="post" class="inline" action="{{ route('admin.plugins.activate', $plugin['slug']) }}">@csrf<button>Activate</button></form>
                    @endif
                </div>
            </article>
        @endforeach
    </div>
@endsection
