@extends('layouts.crm', ['heading' => 'Theme Settings', 'subheading' => 'Switch between five built-in CRM themes and customize the brand theme.'])

@section('content')
    <section class="crm-card" style="max-width:760px;">
        <h2>Appearance</h2>
        <form class="crm-form" method="post" action="{{ route('crm.settings.update') }}">
            @csrf
            @method('put')
            <label>
                <span>Choose Theme</span>
                <select name="crm_theme">
                    @foreach($crmThemes as $key => $theme)
                        <option value="{{ $key }}" @selected(auth()->user()->crm_theme === $key)>{{ $theme['name'] }}</option>
                    @endforeach
                </select>
            </label>
            <div class="crm-grid-3">
                <label><span>Brand Label</span><input name="crm_theme_settings[logo_text]" value="{{ old('crm_theme_settings.logo_text', auth()->user()->crm_theme_settings['logo_text'] ?? 'Apex CRM') }}"></label>
                <label><span>Primary Color</span><input name="crm_theme_settings[primary_color]" value="{{ old('crm_theme_settings.primary_color', auth()->user()->crm_theme_settings['primary_color'] ?? '#7c3aed') }}"></label>
                <label><span>Secondary Color</span><input name="crm_theme_settings[secondary_color]" value="{{ old('crm_theme_settings.secondary_color', auth()->user()->crm_theme_settings['secondary_color'] ?? '#ec4899') }}"></label>
            </div>
            <button class="crm-button-primary" type="submit">Save Theme Settings</button>
        </form>
    </section>
@endsection
