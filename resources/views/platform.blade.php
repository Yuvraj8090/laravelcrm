<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title }}</title>
    <style>
        :root {
            --bg: #f4f1ea;
            --ink: #16212a;
            --muted: #586771;
            --accent: #8f4f2b;
            --card: #fffdf8;
            --border: #ddd3c4;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: "Helvetica Neue", Helvetica, Arial, sans-serif;
            background:
                linear-gradient(135deg, rgba(143, 79, 43, 0.08), transparent 35%),
                linear-gradient(180deg, #faf8f3 0%, var(--bg) 100%);
            color: var(--ink);
        }

        main {
            width: min(1120px, calc(100% - 2rem));
            margin: 0 auto;
            padding: 3rem 0 5rem;
        }

        .hero {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 28px;
            padding: 3rem;
            box-shadow: 0 20px 60px rgba(22, 33, 42, 0.08);
        }

        h1 {
            margin: 0 0 1rem;
            font-size: clamp(2.2rem, 4vw, 4.25rem);
            line-height: 1;
        }

        p {
            color: var(--muted);
            font-size: 1.05rem;
            line-height: 1.7;
            max-width: 48rem;
        }

        ul {
            padding-left: 1.1rem;
            margin-top: 1.5rem;
        }

        li + li {
            margin-top: 0.6rem;
        }

        code {
            background: rgba(22, 33, 42, 0.06);
            padding: 0.12rem 0.35rem;
            border-radius: 0.35rem;
        }

        a {
            color: var(--accent);
        }
    </style>
</head>
<body>
    <main>
        <section class="hero">
            <h1>{{ $title }}</h1>
            <p>
                The platform now has a real Laravel application plus the first CMS building blocks:
                tenancy context, website/domain tables, theme and plugin discovery, content tables,
                and a starter preview route at <code>/sites/starter-site/preview</code>.
            </p>

            <ul>
                @foreach ($documents as $label => $path)
                    <li>{{ $label }}: <code>{{ $path }}</code></li>
                @endforeach
            </ul>
        </section>
    </main>
</body>
</html>
