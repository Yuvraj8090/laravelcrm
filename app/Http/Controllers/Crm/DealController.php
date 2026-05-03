<?php

namespace App\Http\Controllers\Crm;

use App\Crm\Models\Company;
use App\Crm\Models\Contact;
use App\Crm\Models\Deal;
use App\Crm\Models\Pipeline;
use App\Crm\Models\PipelineStage;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DealController extends BaseCrmController
{
    public function index(Request $request): View
    {
        $deal = $request->query('edit')
            ? Deal::findOrFail((int) $request->query('edit'))
            : new Deal(['status' => 'open']);

        $deals = Deal::with(['company', 'contact', 'pipeline', 'stage', 'owner'])->latest()->paginate(12);

        return $this->crmView('crm.deals.index', [
            'deal' => $deal,
            'deals' => $deals,
            'companies' => Company::orderBy('name')->get(),
            'contacts' => Contact::orderBy('first_name')->get(),
            'pipelines' => Pipeline::with('stages')->orderBy('name')->get(),
            'stages' => PipelineStage::with('pipeline')->orderBy('position')->get(),
            'users' => User::orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        Deal::create($this->validated($request));

        return back()->with('status', 'Deal saved.');
    }

    public function update(Request $request, Deal $deal): RedirectResponse
    {
        $deal->update($this->validated($request));

        return redirect()->route('crm.deals.index')->with('status', 'Deal updated.');
    }

    public function destroy(Deal $deal): RedirectResponse
    {
        $deal->delete();

        return back()->with('status', 'Deal deleted.');
    }

    protected function validated(Request $request): array
    {
        return $request->validate([
            'company_id' => ['nullable', 'exists:crm_companies,id'],
            'contact_id' => ['nullable', 'exists:crm_contacts,id'],
            'pipeline_id' => ['nullable', 'exists:crm_pipelines,id'],
            'stage_id' => ['nullable', 'exists:crm_pipeline_stages,id'],
            'name' => ['required', 'string', 'max:255'],
            'value' => ['nullable', 'numeric'],
            'status' => ['required', 'string', 'max:40'],
            'expected_close_at' => ['nullable', 'date'],
            'owner_id' => ['nullable', 'exists:users,id'],
            'notes' => ['nullable', 'string'],
        ]);
    }
}
