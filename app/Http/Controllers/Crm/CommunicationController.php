<?php

namespace App\Http\Controllers\Crm;

use App\Crm\Models\Company;
use App\Crm\Models\Contact;
use App\Crm\Models\Deal;
use App\Crm\Models\EmailMessage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CommunicationController extends BaseCrmController
{
    public function index(Request $request): View
    {
        $email = $request->query('edit')
            ? EmailMessage::findOrFail((int) $request->query('edit'))
            : new EmailMessage(['direction' => 'outbound', 'status' => 'logged']);

        $emails = EmailMessage::with(['company', 'contact', 'deal', 'sender'])->latest()->paginate(12);

        return $this->crmView('crm.communications.index', compact('email', 'emails') + [
            'companies' => Company::orderBy('name')->get(),
            'contacts' => Contact::orderBy('first_name')->get(),
            'deals' => Deal::orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        EmailMessage::create($this->validated($request) + ['sent_by' => $request->user()->id]);

        return back()->with('status', 'Communication logged.');
    }

    public function update(Request $request, EmailMessage $communication): RedirectResponse
    {
        $communication->update($this->validated($request));

        return redirect()->route('crm.communications.index')->with('status', 'Communication updated.');
    }

    public function destroy(EmailMessage $communication): RedirectResponse
    {
        $communication->delete();

        return back()->with('status', 'Communication deleted.');
    }

    protected function validated(Request $request): array
    {
        return $request->validate([
            'company_id' => ['nullable', 'exists:crm_companies,id'],
            'contact_id' => ['nullable', 'exists:crm_contacts,id'],
            'deal_id' => ['nullable', 'exists:crm_deals,id'],
            'subject' => ['required', 'string', 'max:255'],
            'direction' => ['required', 'string', 'max:20'],
            'status' => ['required', 'string', 'max:20'],
            'body' => ['required', 'string'],
            'sent_at' => ['nullable', 'date'],
        ]);
    }
}
