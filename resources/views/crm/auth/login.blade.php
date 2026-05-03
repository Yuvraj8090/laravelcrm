<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>CRM Login</title>
    <link rel="stylesheet" href="{{ asset('crm-assets/base.css') }}">
    <link rel="stylesheet" href="{{ asset('crm-assets/themes/corporate.css') }}">
</head>
<body class="crm-login">
    <div class="crm-login-card">
        <h1>Welcome to the CRM</h1>
        <p class="crm-help">Use one of the seeded CRM accounts to access admin, manager, or sales dashboards.</p>
        @if($errors->any())
            <div class="crm-errors">{{ $errors->first() }}</div>
        @endif
        <form method="post" action="{{ route('crm.login.store') }}" class="crm-form">
            @csrf
            <label><span>Email</span><input type="email" name="email" value="{{ old('email') }}" required></label>
            <label><span>Password</span><input type="password" name="password" required></label>
            <button class="crm-button-primary" type="submit">Sign in</button>
        </form>
        <div class="crm-help" style="margin-top:1rem;">
            Admin: <strong>crm.admin@example.com</strong><br>
            Manager: <strong>crm.manager@example.com</strong><br>
            Sales Rep: <strong>crm.sales@example.com</strong><br>
            Password for all demo users: <strong>password123</strong>
        </div>
    </div>
</body>
</html>
