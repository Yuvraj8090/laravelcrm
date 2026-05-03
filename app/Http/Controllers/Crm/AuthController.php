<?php

namespace App\Http\Controllers\Crm;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthController extends BaseCrmController
{
    public function create(): View
    {
        return $this->crmView('crm.auth.login');
    }

    public function store(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (!Auth::attempt($credentials, $request->boolean('remember'))) {
            return back()->withErrors(['email' => 'The CRM credentials are invalid.'])->onlyInput('email');
        }

        if (!Auth::user()?->hasCrmRole(['admin', 'manager', 'sales_rep'])) {
            Auth::logout();

            return back()->withErrors(['email' => 'This account does not have CRM access.'])->onlyInput('email');
        }

        $request->session()->regenerate();

        return redirect()->route('crm.dashboard');
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('crm.login');
    }
}
