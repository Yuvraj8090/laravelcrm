<?php

namespace App\Http\Controllers\Crm;

use App\Crm\Models\Company;
use App\Crm\Models\Contact;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ContactController extends BaseCrmController
{
    public function index(Request $request): View
    {
        $contact = $request->query('edit')
            ? Contact::findOrFail((int) $request->query('edit'))
            : new Contact();

        $contacts = Contact::with(['company', 'owner'])->latest()->paginate(12);
        $companies = Company::orderBy('name')->get();
        $users = User::orderBy('name')->get();

        return $this->crmView('crm.contacts.index', compact('contact', 'contacts', 'companies', 'users'));
    }

    public function store(Request $request): RedirectResponse
    {
        Contact::create($this->validated($request));

        return back()->with('status', 'Contact saved.');
    }

    public function update(Request $request, Contact $contact): RedirectResponse
    {
        $contact->update($this->validated($request));

        return redirect()->route('crm.contacts.index')->with('status', 'Contact updated.');
    }

    public function destroy(Contact $contact): RedirectResponse
    {
        $contact->delete();

        return back()->with('status', 'Contact deleted.');
    }

    protected function validated(Request $request): array
    {
        return $request->validate([
            'company_id' => ['nullable', 'exists:crm_companies,id'],
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email'],
            'phone' => ['nullable', 'string', 'max:50'],
            'title' => ['nullable', 'string', 'max:255'],
            'status' => ['required', 'string', 'max:40'],
            'owner_id' => ['nullable', 'exists:users,id'],
            'last_contacted_at' => ['nullable', 'date'],
            'notes' => ['nullable', 'string'],
        ]);
    }
}
