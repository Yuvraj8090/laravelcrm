@extends('layouts.crm', ['heading' => 'Invoices', 'subheading' => 'Create low-cost invoice records without relying on paid billing services.'])

@section('content')
    <div class="crm-grid-2">
        <section class="crm-card">
            <h2>{{ $invoice->exists ? 'Edit Invoice' : 'Create Invoice' }}</h2>
            <form class="crm-form" method="post" action="{{ $invoice->exists ? route('crm.invoices.update', $invoice) : route('crm.invoices.store') }}">
                @csrf
                @if($invoice->exists) @method('put') @endif
                <div class="crm-grid-2">
                    <label><span>Invoice Number</span><input name="invoice_number" value="{{ old('invoice_number', $invoice->invoice_number ?? 'INV-'.now()->format('YmdHis')) }}" required></label>
                    <label><span>Status</span><input name="status" value="{{ old('status', $invoice->status ?: 'draft') }}"></label>
                </div>
                <div class="crm-grid-2">
                    <label><span>Issue Date</span><input type="date" name="issue_date" value="{{ old('issue_date', optional($invoice->issue_date)->format('Y-m-d') ?: now()->toDateString()) }}"></label>
                    <label><span>Due Date</span><input type="date" name="due_date" value="{{ old('due_date', optional($invoice->due_date)->format('Y-m-d')) }}"></label>
                </div>
                <label><span>Company</span><select name="company_id"><option value="">None</option>@foreach($companies as $companyOption)<option value="{{ $companyOption->id }}" @selected((string) old('company_id', $invoice->company_id) === (string) $companyOption->id)>{{ $companyOption->name }}</option>@endforeach</select></label>
                <label><span>Contact</span><select name="contact_id"><option value="">None</option>@foreach($contacts as $contactOption)<option value="{{ $contactOption->id }}" @selected((string) old('contact_id', $invoice->contact_id) === (string) $contactOption->id)>{{ $contactOption->first_name }} {{ $contactOption->last_name }}</option>@endforeach</select></label>
                <label><span>Deal</span><select name="deal_id"><option value="">None</option>@foreach($deals as $dealOption)<option value="{{ $dealOption->id }}" @selected((string) old('deal_id', $invoice->deal_id) === (string) $dealOption->id)>{{ $dealOption->name }}</option>@endforeach</select></label>
                <div class="crm-inline-items">
                    @php($items = old('items', $invoice->exists ? $invoice->items->toArray() : [['description' => '', 'quantity' => 1, 'unit_price' => 0, 'total' => 0], ['description' => '', 'quantity' => 1, 'unit_price' => 0, 'total' => 0]]))
                    @foreach($items as $index => $item)
                        <div class="crm-inline-item">
                            <input name="items[{{ $index }}][description]" placeholder="Item description" value="{{ $item['description'] ?? '' }}">
                            <input type="number" step="0.01" name="items[{{ $index }}][quantity]" placeholder="Qty" value="{{ $item['quantity'] ?? 1 }}">
                            <input type="number" step="0.01" name="items[{{ $index }}][unit_price]" placeholder="Unit price" value="{{ $item['unit_price'] ?? 0 }}">
                            <input type="number" step="0.01" name="items[{{ $index }}][total]" placeholder="Total" value="{{ $item['total'] ?? 0 }}">
                        </div>
                    @endforeach
                </div>
                <div class="crm-grid-3">
                    <label><span>Subtotal</span><input type="number" step="0.01" name="subtotal" value="{{ old('subtotal', $invoice->subtotal) }}"></label>
                    <label><span>Tax</span><input type="number" step="0.01" name="tax" value="{{ old('tax', $invoice->tax) }}"></label>
                    <label><span>Total</span><input type="number" step="0.01" name="total" value="{{ old('total', $invoice->total) }}"></label>
                </div>
                <label><span>Notes</span><textarea name="notes">{{ old('notes', $invoice->notes) }}</textarea></label>
                <button class="crm-button-primary" type="submit">{{ $invoice->exists ? 'Update Invoice' : 'Create Invoice' }}</button>
            </form>
        </section>

        <section class="crm-card">
            <h2>Invoices</h2>
            <div class="crm-table-wrap">
                <table>
                    <thead><tr><th>Invoice</th><th>Company</th><th>Status</th><th>Total</th><th>Actions</th></tr></thead>
                    <tbody>
                    @foreach($invoices as $item)
                        <tr>
                            <td><strong>{{ $item->invoice_number }}</strong><div class="muted">{{ optional($item->issue_date)->format('d M Y') }}</div></td>
                            <td>{{ $item->company?->name ?? 'No company' }}</td>
                            <td><span class="crm-badge">{{ $item->status }}</span></td>
                            <td>{{ number_format($item->total, 2) }}</td>
                            <td class="crm-actions">
                                <a class="crm-button crm-button-secondary" href="{{ route('crm.invoices.index', ['edit' => $item->id]) }}">Edit</a>
                                <form method="post" action="{{ route('crm.invoices.destroy', $item) }}">@csrf @method('delete')<button class="crm-button-danger" type="submit">Delete</button></form>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
            <div style="margin-top:1rem;">{{ $invoices->links() }}</div>
        </section>
    </div>
@endsection
