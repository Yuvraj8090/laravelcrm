@extends('layouts.admin', ['heading' => 'Theme Settings', 'subheading' => 'Colors, logo, fonts, and layout controls for the selected theme.'])

@section('content')
    <form method="post" action="{{ route('admin.themes.settings.update', $slug) }}" class="card">
        @csrf
        @method('put')
        <div class="grid-2">
            <div class="field"><label>Primary Color</label><input name="settings[colors.primary]" value="{{ old('settings.colors.primary', $settings['colors.primary'] ?? '#0f6b63') }}"></div>
            <div class="field"><label>Accent Color</label><input name="settings[colors.accent]" value="{{ old('settings.colors.accent', $settings['colors.accent'] ?? '#8f4b2f') }}"></div>
            <div class="field"><label>Logo URL</label><input name="settings[branding.logo]" value="{{ old('settings.branding.logo', $settings['branding.logo'] ?? '') }}"></div>
            <div class="field"><label>Font Family</label><input name="settings[typography.font]" value="{{ old('settings.typography.font', $settings['typography.font'] ?? 'Georgia') }}"></div>
            <div class="field"><label>Layout Width</label><input name="settings[layout.width]" value="{{ old('settings.layout.width', $settings['layout.width'] ?? '1200px') }}"></div>
        </div>
        <button type="submit">Save theme settings</button>
    </form>
@endsection
