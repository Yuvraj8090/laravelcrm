<?php

namespace App\Http\Controllers\Crm;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SettingsController extends BaseCrmController
{
    public function edit(): View
    {
        return $this->crmView('crm.settings.theme');
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'crm_theme' => ['required', 'in:corporate,dark,minimal,contrast,brand'],
            'crm_theme_settings.logo_text' => ['nullable', 'string', 'max:120'],
            'crm_theme_settings.primary_color' => ['nullable', 'string', 'max:20'],
            'crm_theme_settings.secondary_color' => ['nullable', 'string', 'max:20'],
        ]);

        $request->user()->update([
            'crm_theme' => $validated['crm_theme'],
            'crm_theme_settings' => $validated['crm_theme_settings'] ?? [],
        ]);

        return back()->with('status', 'CRM theme settings updated.');
    }
}
