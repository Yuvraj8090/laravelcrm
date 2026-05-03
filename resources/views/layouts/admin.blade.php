<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'CMS Admin' }}</title>
    <style>
        :root {
            --bg: #f3efe6;
            --surface: #fffdf8;
            --surface-2: #f8f5ed;
            --ink: #1d2b34;
            --muted: #5f6e78;
            --line: #d8d0c0;
            --brand: #0f6b63;
            --brand-2: #8f4b2f;
            --danger: #b33a3a;
            --radius: 18px;
        }

        * { box-sizing: border-box; }
        body {
            margin: 0;
            font-family: "Helvetica Neue", Arial, sans-serif;
            color: var(--ink);
            background:
                radial-gradient(circle at top left, rgba(15, 107, 99, 0.12), transparent 26%),
                linear-gradient(180deg, #faf7f1, var(--bg));
        }

        a { color: var(--brand); text-decoration: none; }
        .shell { display: grid; grid-template-columns: 280px 1fr; min-height: 100vh; }
        .sidebar {
            background: rgba(255,255,255,0.72);
            border-right: 1px solid var(--line);
            backdrop-filter: blur(8px);
            padding: 1.5rem;
            position: sticky;
            top: 0;
            height: 100vh;
        }
        .brand { font-size: 1.35rem; font-weight: 700; margin-bottom: 1rem; }
        .brand small { display: block; color: var(--muted); font-size: 0.85rem; margin-top: 0.3rem; }
        .nav a {
            display: block;
            padding: 0.8rem 0.95rem;
            border-radius: 14px;
            color: var(--ink);
            margin-bottom: 0.35rem;
        }
        .nav a:hover, .nav a.active { background: var(--surface); }
        .content { padding: 1.75rem; }
        .topbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 1rem;
            margin-bottom: 1.5rem;
        }
        .page-title h1 { margin: 0; font-size: 2rem; }
        .page-title p { margin: 0.35rem 0 0; color: var(--muted); }
        .card {
            background: var(--surface);
            border: 1px solid var(--line);
            border-radius: var(--radius);
            padding: 1.2rem;
            box-shadow: 0 16px 40px rgba(29, 43, 52, 0.06);
        }
        .stack { display: grid; gap: 1rem; }
        .grid-2 { display: grid; gap: 1rem; grid-template-columns: repeat(2, minmax(0, 1fr)); }
        .grid-3 { display: grid; gap: 1rem; grid-template-columns: repeat(3, minmax(0, 1fr)); }
        .stats { display: grid; gap: 1rem; grid-template-columns: repeat(4, minmax(0, 1fr)); }
        .stat strong { font-size: 2rem; display: block; }
        .muted { color: var(--muted); }
        .btn, button, input[type=submit] {
            appearance: none;
            border: 1px solid transparent;
            background: var(--brand);
            color: white;
            border-radius: 12px;
            padding: 0.7rem 1rem;
            cursor: pointer;
            font: inherit;
        }
        .btn.secondary, button.secondary { background: var(--surface-2); color: var(--ink); border-color: var(--line); }
        .btn.danger, button.danger { background: var(--danger); }
        form.inline { display: inline; }
        input, select, textarea {
            width: 100%;
            padding: 0.75rem 0.85rem;
            border: 1px solid var(--line);
            border-radius: 12px;
            font: inherit;
            background: white;
        }
        textarea { min-height: 140px; resize: vertical; }
        label { display: block; font-weight: 600; margin-bottom: 0.4rem; }
        .field { margin-bottom: 1rem; }
        .table-wrap { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 0.85rem 0.75rem; border-bottom: 1px solid var(--line); text-align: left; vertical-align: top; }
        th { color: var(--muted); font-size: 0.9rem; }
        .badge {
            display: inline-block;
            padding: 0.25rem 0.55rem;
            border-radius: 999px;
            background: rgba(15, 107, 99, 0.12);
            color: var(--brand);
            font-size: 0.85rem;
        }
        .flash, .errors {
            margin-bottom: 1rem;
            border-radius: 14px;
            padding: 0.9rem 1rem;
        }
        .flash { background: #e8faf4; color: #0e5d44; }
        .errors { background: #fff1f1; color: #9b2226; }
        .chip-list { display: flex; flex-wrap: wrap; gap: 0.5rem; }
        .chip { background: var(--surface-2); border: 1px solid var(--line); border-radius: 999px; padding: 0.35rem 0.7rem; }
        .actions { display: flex; gap: 0.5rem; flex-wrap: wrap; }
        .split { display: flex; justify-content: space-between; align-items: center; gap: 1rem; }
        @media (max-width: 980px) {
            .shell { grid-template-columns: 1fr; }
            .sidebar { position: static; height: auto; }
            .stats, .grid-2, .grid-3 { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
<div class="shell">
    <aside class="sidebar">
        <div class="brand">
            LaravelCMS
            <small>{{ $currentWebsite->name ?? 'No site selected' }}</small>
        </div>

        @isset($adminWebsites)
            <form method="get" action="{{ url()->current() }}" class="field">
                <label for="website_id">Current website</label>
                <select id="website_id" name="website_id" onchange="this.form.submit()">
                    @foreach($adminWebsites as $site)
                        <option value="{{ $site->id }}" @selected(($currentWebsite->id ?? null) === $site->id)>{{ $site->name }}</option>
                    @endforeach
                </select>
            </form>
        @endisset

        <nav class="nav">
            <a href="{{ route('admin.dashboard') }}">Dashboard</a>
            <a href="{{ route('admin.websites.index') }}">Websites</a>
            <a href="{{ route('admin.contents.index', ['type' => 'page']) }}">Pages</a>
            <a href="{{ route('admin.contents.index', ['type' => 'post']) }}">Posts</a>
            <a href="{{ route('admin.taxonomies.index') }}">Categories & Tags</a>
            <a href="{{ route('admin.media.index') }}">Media Library</a>
            <a href="{{ route('admin.themes.index') }}">Themes</a>
            <a href="{{ route('admin.plugins.index') }}">Plugins</a>
            <a href="{{ route('admin.menus.index') }}">Menus</a>
            <a href="{{ route('admin.widgets.index') }}">Widgets</a>
        </nav>

        <form method="post" action="{{ route('logout') }}" style="margin-top: 1.5rem;">
            @csrf
            <button class="secondary" type="submit">Sign out</button>
        </form>
    </aside>

    <main class="content">
        @if(session('status'))
            <div class="flash">{{ session('status') }}</div>
        @endif

        @if($errors->any())
            <div class="errors">
                @foreach($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        <div class="topbar">
            <div class="page-title">
                <h1>{{ $heading ?? 'CMS Admin' }}</h1>
                @isset($subheading)
                    <p>{{ $subheading }}</p>
                @endisset
            </div>
            @isset($topActions)
                <div class="actions">{!! $topActions !!}</div>
            @endisset
        </div>

        @yield('content')
    </main>
</div>
</body>
</html>
