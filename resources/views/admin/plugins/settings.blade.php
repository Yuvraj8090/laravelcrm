@extends('layouts.admin', ['heading' => 'Plugin Settings', 'subheading' => 'Configure plugin-specific settings for the current website.'])

@section('content')
    <form method="post" action="{{ route('admin.plugins.settings.update', $plugin['slug']) }}" class="card">
        @csrf
        @method('put')
        <div class="grid-2">
            <div class="field"><label>Enabled Features</label><input name="settings[features]" value="{{ old('settings.features', $settings['features'] ?? '') }}"></div>
            <div class="field"><label>Webhook URL</label><input name="settings[webhook_url]" value="{{ old('settings.webhook_url', $settings['webhook_url'] ?? '') }}"></div>
            <div class="field"><label>API Key</label><input name="settings[api_key]" value="{{ old('settings.api_key', $settings['api_key'] ?? '') }}"></div>
        </div>
        <button type="submit">Save plugin settings</button>
    </form>
@endsection
