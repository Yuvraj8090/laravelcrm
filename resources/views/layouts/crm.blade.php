@php
    $currentTheme = auth()->user()?->crm_theme ?? 'corporate';
    $themeSettings = auth()->user()?->crm_theme_settings ?? [];
    $brandPrimary = $themeSettings['primary_color'] ?? '#7c3aed';
    $brandSecondary = $themeSettings['secondary_color'] ?? '#ec4899';
    $brandLabel = $themeSettings['logo_text'] ?? 'Apex CRM';
    $crmRoutes = [
        'dashboard' => route('crm.dashboard'),
        'companies' => route('crm.companies.index'),
        'contacts' => route('crm.contacts.index'),
        'leads' => route('crm.leads.index'),
        'deals' => route('crm.deals.index'),
        'tasks' => route('crm.tasks.index'),
        'communications' => route('crm.communications.index'),
        'pipelines' => route('crm.pipelines.index'),
        'quotes' => route('crm.quotes.index'),
        'invoices' => route('crm.invoices.index'),
        'settings' => route('crm.settings.edit'),
    ];
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Laravel CRM' }}</title>
    <link rel="stylesheet" href="{{ asset('crm-assets/base.css') }}">
    <link rel="stylesheet" href="{{ asset('crm-assets/themes/'.(config('crm.themes')[$currentTheme]['file'] ?? 'corporate.css')) }}">
    @if($currentTheme === 'brand')
        <style>
            body {
                --brand-primary: {{ $brandPrimary }};
                --brand-secondary: {{ $brandSecondary }};
            }
        </style>
    @endif
</head>
<body>
<div class="crm-shell">
    <aside class="crm-sidebar">
        <div class="crm-brand">
            <div class="crm-brand-mark">{{ strtoupper(substr($brandLabel, 0, 2)) }}</div>
            <div>
                <strong>{{ $brandLabel }}</strong>
                <div class="muted">Laravel CRM Suite</div>
            </div>
        </div>
        <nav class="crm-sidebar-nav">
            @foreach($crmNav as $key => $label)
                <a href="{{ $crmRoutes[$key] ?? '#' }}" class="{{ request()->routeIs('crm.'.$key.'.*') || ($key === 'dashboard' && request()->routeIs('crm.dashboard')) || ($key === 'settings' && request()->routeIs('crm.settings.*')) ? 'active' : '' }}">
                    {{ $label }}
                </a>
            @endforeach
        </nav>
    </aside>

    <main class="crm-main">
        @if(session('status'))
            <div class="crm-flash">{{ session('status') }}</div>
        @endif
        @if($errors->any())
            <div class="crm-errors">
                @foreach($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        <div class="crm-topbar">
            <div class="crm-title">
                <h1>{{ $heading ?? 'CRM Workspace' }}</h1>
                <p>{{ $subheading ?? 'Manage your revenue pipeline and client relationships.' }}</p>
            </div>
            <div class="crm-toolbar">
                <span class="crm-branding-chip">{{ auth()->user()?->crm_role }}</span>
                <a class="crm-button crm-button-secondary" href="{{ route('crm.settings.edit') }}">Theme</a>
                <form method="post" action="{{ route('crm.logout') }}">
                    @csrf
                    <button class="crm-button crm-button-secondary" type="submit">Logout</button>
                </form>
            </div>
        </div>

        @yield('content')
    </main>
</div>
</body>
</html>
