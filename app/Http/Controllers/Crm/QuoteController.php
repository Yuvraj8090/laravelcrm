<?php

namespace App\Http\Controllers\Crm;

use App\Crm\Models\Company;
use App\Crm\Models\Contact;
use App\Crm\Models\Deal;
use App\Crm\Models\Quote;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class QuoteController extends BaseCrmController
{
    public function index(Request $request): View
    {
        $quote = $request->query('edit')
            ? Quote::with('items')->findOrFail((int) $request->query('edit'))
            : new Quote(['status' => 'draft', 'issue_date' => now()->toDateString()]);

        $quotes = Quote::with(['company', 'contact', 'deal', 'creator'])->latest()->paginate(12);

        return $this->crmView('crm.quotes.index', compact('quote', 'quotes') + [
            'companies' => Company::orderBy('name')->get(),
            'contacts' => Contact::orderBy('first_name')->get(),
            'deals' => Deal::orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $quote = Quote::create($this->validated($request) + ['created_by' => $request->user()->id]);
        $this->syncItems($quote, $request->input('items', []));

        return back()->with('status', 'Quote saved.');
    }

    public function update(Request $request, Quote $quote): RedirectResponse
    {
        $quote->update($this->validated($request));
        $quote->items()->delete();
        $this->syncItems($quote, $request->input('items', []));

        return redirect()->route('crm.quotes.index')->with('status', 'Quote updated.');
    }

    public function destroy(Quote $quote): RedirectResponse
    {
        $quote->delete();

        return back()->with('status', 'Quote deleted.');
    }

    protected function validated(Request $request): array
    {
        $quoteId = $request->route('quote')?->id;

        return $request->validate([
            'company_id' => ['nullable', 'exists:crm_companies,id'],
            'contact_id' => ['nullable', 'exists:crm_contacts,id'],
            'deal_id' => ['nullable', 'exists:crm_deals,id'],
            'quote_number' => ['required', 'string', 'max:255', Rule::unique('crm_quotes', 'quote_number')->ignore($quoteId)],
            'status' => ['required', 'string', 'max:30'],
            'issue_date' => ['required', 'date'],
            'valid_until' => ['nullable', 'date'],
            'subtotal' => ['nullable', 'numeric'],
            'tax' => ['nullable', 'numeric'],
            'total' => ['nullable', 'numeric'],
            'notes' => ['nullable', 'string'],
        ]);
    }

    protected function syncItems(Quote $quote, array $items): void
    {
        foreach ($items as $item) {
            if (!filled($item['description'] ?? null)) {
                continue;
            }

            $quote->items()->create([
                'description' => $item['description'],
                'quantity' => $item['quantity'] ?? 1,
                'unit_price' => $item['unit_price'] ?? 0,
                'total' => $item['total'] ?? 0,
            ]);
        }
    }
}
