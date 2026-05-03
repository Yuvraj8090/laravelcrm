<?php

namespace App\Http\Controllers\Crm;

use App\Crm\Models\Company;
use App\Crm\Models\Contact;
use App\Crm\Models\Deal;
use App\Crm\Models\Invoice;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class InvoiceController extends BaseCrmController
{
    public function index(Request $request): View
    {
        $invoice = $request->query('edit')
            ? Invoice::with('items')->findOrFail((int) $request->query('edit'))
            : new Invoice(['status' => 'draft', 'issue_date' => now()->toDateString()]);

        $invoices = Invoice::with(['company', 'contact', 'deal', 'creator'])->latest()->paginate(12);

        return $this->crmView('crm.invoices.index', compact('invoice', 'invoices') + [
            'companies' => Company::orderBy('name')->get(),
            'contacts' => Contact::orderBy('first_name')->get(),
            'deals' => Deal::orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $invoice = Invoice::create($this->validated($request) + ['created_by' => $request->user()->id]);
        $this->syncItems($invoice, $request->input('items', []));

        return back()->with('status', 'Invoice saved.');
    }

    public function update(Request $request, Invoice $invoice): RedirectResponse
    {
        $invoice->update($this->validated($request));
        $invoice->items()->delete();
        $this->syncItems($invoice, $request->input('items', []));

        return redirect()->route('crm.invoices.index')->with('status', 'Invoice updated.');
    }

    public function destroy(Invoice $invoice): RedirectResponse
    {
        $invoice->delete();

        return back()->with('status', 'Invoice deleted.');
    }

    protected function validated(Request $request): array
    {
        $invoiceId = $request->route('invoice')?->id;

        return $request->validate([
            'company_id' => ['nullable', 'exists:crm_companies,id'],
            'contact_id' => ['nullable', 'exists:crm_contacts,id'],
            'deal_id' => ['nullable', 'exists:crm_deals,id'],
            'invoice_number' => ['required', 'string', 'max:255', Rule::unique('crm_invoices', 'invoice_number')->ignore($invoiceId)],
            'status' => ['required', 'string', 'max:30'],
            'issue_date' => ['required', 'date'],
            'due_date' => ['nullable', 'date'],
            'subtotal' => ['nullable', 'numeric'],
            'tax' => ['nullable', 'numeric'],
            'total' => ['nullable', 'numeric'],
            'notes' => ['nullable', 'string'],
        ]);
    }

    protected function syncItems(Invoice $invoice, array $items): void
    {
        foreach ($items as $item) {
            if (!filled($item['description'] ?? null)) {
                continue;
            }

            $invoice->items()->create([
                'description' => $item['description'],
                'quantity' => $item['quantity'] ?? 1,
                'unit_price' => $item['unit_price'] ?? 0,
                'total' => $item['total'] ?? 0,
            ]);
        }
    }
}
