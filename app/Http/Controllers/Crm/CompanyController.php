<?php

namespace App\Http\Controllers\Crm;

use App\Crm\Models\Company;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CompanyController extends BaseCrmController
{
    public function index(Request $request): View
    {
        $company = $request->query('edit')
            ? Company::findOrFail((int) $request->query('edit'))
            : new Company();

        $companies = Company::with('owner')->latest()->paginate(12);
        $users = User::orderBy('name')->get();

        return $this->crmView('crm.companies.index', compact('company', 'companies', 'users'));
    }

    public function store(Request $request): RedirectResponse
    {
        Company::create($this->validated($request));

        return back()->with('status', 'Company saved.');
    }

    public function update(Request $request, Company $company): RedirectResponse
    {
        $company->update($this->validated($request));

        return redirect()->route('crm.companies.index')->with('status', 'Company updated.');
    }

    public function destroy(Company $company): RedirectResponse
    {
        $company->delete();

        return back()->with('status', 'Company deleted.');
    }

    protected function validated(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'industry' => ['nullable', 'string', 'max:255'],
            'website' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email'],
            'phone' => ['nullable', 'string', 'max:50'],
            'address' => ['nullable', 'string', 'max:255'],
            'owner_id' => ['nullable', 'exists:users,id'],
            'notes' => ['nullable', 'string'],
        ]);
    }
}
