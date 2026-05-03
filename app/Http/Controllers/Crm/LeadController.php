<?php

namespace App\Http\Controllers\Crm;

use App\Crm\Models\Company;
use App\Crm\Models\Contact;
use App\Crm\Models\Deal;
use App\Crm\Models\Lead;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LeadController extends BaseCrmController
{
    public function index(Request $request): View
    {
        $lead = $request->query('edit')
            ? Lead::findOrFail((int) $request->query('edit'))
            : new Lead(['status' => 'new', 'priority' => 'medium']);

        $leads = Lead::with(['company', 'contact', 'owner', 'deal'])->latest()->paginate(12);

        return $this->crmView('crm.leads.index', [
            'lead' => $lead,
            'leads' => $leads,
            'companies' => Company::orderBy('name')->get(),
            'contacts' => Contact::orderBy('first_name')->get(),
            'deals' => Deal::orderBy('name')->get(),
            'users' => User::orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        Lead::create($this->validated($request));

        return back()->with('status', 'Lead saved.');
    }

    public function update(Request $request, Lead $lead): RedirectResponse
    {
        $lead->update($this->validated($request));

        return redirect()->route('crm.leads.index')->with('status', 'Lead updated.');
    }

    public function destroy(Lead $lead): RedirectResponse
    {
        $lead->delete();

        return back()->with('status', 'Lead deleted.');
    }

    protected function validated(Request $request): array
    {
        return $request->validate([
            'company_id' => ['nullable', 'exists:crm_companies,id'],
            'contact_id' => ['nullable', 'exists:crm_contacts,id'],
            'converted_deal_id' => ['nullable', 'exists:crm_deals,id'],
            'title' => ['required', 'string', 'max:255'],
            'source' => ['nullable', 'string', 'max:255'],
            'value' => ['nullable', 'numeric'],
            'status' => ['required', 'string', 'max:40'],
            'priority' => ['required', 'string', 'max:30'],
            'owner_id' => ['nullable', 'exists:users,id'],
            'notes' => ['nullable', 'string'],
        ]);
    }
}
