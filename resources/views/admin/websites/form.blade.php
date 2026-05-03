@extends('layouts.admin', ['heading' => $website->exists ? 'Edit Website' : 'Create Website', 'subheading' => 'Manage domains, locale, status, and theme assignment.'])

@section('content')
    <form method="post" action="{{ $website->exists ? route('admin.websites.update', $website) : route('admin.websites.store') }}" class="card">
        @csrf
        @if($website->exists) @method('put') @endif
        <div class="grid-2">
            <div class="field"><label>Name</label><input name="name" value="{{ old('name', $website->name) }}" required></div>
            <div class="field"><label>Slug</label><input name="slug" value="{{ old('slug', $website->slug) }}" required></div>
            <div class="field"><label>Primary Domain</label><input name="primary_domain" value="{{ old('primary_domain', $website->primary_domain) }}"></div>
            <div class="field"><label>Theme Slug</label><input name="theme_slug" value="{{ old('theme_slug', $website->theme_slug) }}"></div>
            <div class="field"><label>Status</label><select name="status">@foreach(['active','inactive','maintenance'] as $status)<option value="{{ $status }}" @selected(old('status', $website->status ?: 'active') === $status)>{{ ucfirst($status) }}</option>@endforeach</select></div>
            <div class="field"><label>Locale</label><input name="locale" value="{{ old('locale', $website->locale ?: 'en') }}"></div>
            <div class="field"><label>Timezone</label><input name="timezone" value="{{ old('timezone', $website->timezone ?: 'UTC') }}"></div>
        </div>
        <button type="submit">{{ $website->exists ? 'Save changes' : 'Create website' }}</button>
    </form>
@endsection
