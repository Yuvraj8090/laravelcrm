<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login</title>
    <style>
        body { margin: 0; min-height: 100vh; display: grid; place-items: center; background: linear-gradient(180deg, #f7f2ea, #ece6d7); font-family: Arial, sans-serif; color: #1d2b34; }
        .card { width: min(420px, calc(100% - 2rem)); background: white; border-radius: 24px; padding: 2rem; box-shadow: 0 24px 60px rgba(29,43,52,.12); }
        label { display: block; margin-bottom: .4rem; font-weight: 600; }
        input { width: 100%; padding: .8rem .9rem; border-radius: 12px; border: 1px solid #d8d0c0; margin-bottom: 1rem; }
        button { width: 100%; padding: .85rem 1rem; border: 0; border-radius: 12px; background: #0f6b63; color: white; font: inherit; cursor: pointer; }
        .hint { color: #5f6e78; font-size: .95rem; }
        .errors { background: #fff1f1; color: #9b2226; padding: .75rem 1rem; border-radius: 12px; margin-bottom: 1rem; }
    </style>
</head>
<body>
<div class="card">
    <h1>CMS Login</h1>
    <p class="hint">Use a seeded admin account to enter the dashboard.</p>
    @if($errors->any())
        <div class="errors">{{ $errors->first() }}</div>
    @endif
    <form method="post" action="{{ route('login.store') }}">
        @csrf
        <label>Email</label>
        <input type="email" name="email" value="{{ old('email') }}" required>
        <label>Password</label>
        <input type="password" name="password" required>
        <button type="submit">Sign in</button>
    </form>
</div>
</body>
</html>
