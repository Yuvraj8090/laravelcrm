@extends('layouts.admin', ['heading' => 'Themes', 'subheading' => 'Activate, preview, and configure installed themes with a safe fallback.'])

@section('content')
    <div class="grid-3">
        @foreach($themes as $theme)
            <article class="card">
                <h2 style="margin-top:0;">{{ $theme['name'] }}</h2>
                <p class="muted">{{ $theme['description'] ?? 'No description' }}</p>
                <div class="chip-list" style="margin-bottom:1rem;">
                    <span class="chip">{{ $theme['slug'] }}</span>
                    <span class="chip">v{{ $theme['version'] }}</span>
                    @if($website->theme_slug === $theme['slug'])<span class="badge">Active</span>@endif
                </div>
                <div class="actions">
                    <a class="btn secondary" href="{{ route('admin.themes.preview', $theme['slug']) }}" target="_blank">Preview</a>
                    <a class="btn secondary" href="{{ route('admin.themes.settings', $theme['slug']) }}">Settings</a>
                    <form method="post" class="inline" action="{{ route('admin.themes.activate', $theme['slug']) }}">@csrf<button>Activate</button></form>
                </div>
            </article>
        @endforeach
    </div>
@endsection
