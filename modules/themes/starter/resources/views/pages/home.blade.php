<!DOCTYPE html>
<html lang="{{ $website->locale }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $pageTitle }}</title>
    <style>
        :root {
            color-scheme: light;
            --bg: #f7f5ef;
            --surface: #fffdf8;
            --ink: #1d2a33;
            --muted: #5d6a73;
            --accent: #0d6c63;
            --border: #ded7c8;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: Georgia, "Times New Roman", serif;
            background:
                radial-gradient(circle at top left, rgba(13, 108, 99, 0.12), transparent 28%),
                linear-gradient(180deg, #faf7f0 0%, var(--bg) 100%);
            color: var(--ink);
        }

        .shell {
            width: min(1080px, calc(100% - 2rem));
            margin: 0 auto;
            padding: 3rem 0 5rem;
        }

        .hero {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 28px;
            padding: 3rem;
            box-shadow: 0 20px 60px rgba(29, 42, 51, 0.08);
        }

        .eyebrow {
            color: var(--accent);
            text-transform: uppercase;
            letter-spacing: 0.18em;
            font-size: 0.78rem;
            margin: 0 0 1rem;
        }

        h1 {
            margin: 0 0 1rem;
            font-size: clamp(2.5rem, 6vw, 5rem);
            line-height: 0.98;
        }

        p {
            margin: 0;
            max-width: 42rem;
            color: var(--muted);
            font-size: 1.1rem;
            line-height: 1.7;
        }

        .grid {
            display: grid;
            gap: 1rem;
            margin-top: 2rem;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        }

        .card {
            background: rgba(255, 255, 255, 0.72);
            border: 1px solid var(--border);
            border-radius: 20px;
            padding: 1.25rem;
        }

        .card strong {
            display: block;
            margin-bottom: 0.5rem;
        }
    </style>
</head>
<body>
    <main class="shell">
        <section class="hero">
            <p class="eyebrow">Starter Theme</p>
            <h1>{{ $website->name }}</h1>
            <p>
                This preview confirms the CMS foundation is loading a tenant-aware theme from the new
                theme registry. Next up we can add the admin panel, theme settings, and builder-backed pages.
            </p>

            <div class="grid">
                <article class="card">
                    <strong>Website slug</strong>
                    <span>{{ $website->slug }}</span>
                </article>
                <article class="card">
                    <strong>Primary domain</strong>
                    <span>{{ $website->primary_domain }}</span>
                </article>
                <article class="card">
                    <strong>Active theme</strong>
                    <span>{{ $activeTheme['name'] ?? 'Unknown theme' }}</span>
                </article>
            </div>
        </section>
    </main>
</body>
</html>
