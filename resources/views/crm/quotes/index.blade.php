@extends('layouts.crm', ['heading' => 'Quotes', 'subheading' => 'Generate customer-ready quotes using built-in Laravel forms and storage.'])

@section('content')
    <div class="crm-grid-2">
        <section class="crm-card">
            <h2>{{ $quote->exists ? 'Edit Quote' : 'Create Quote' }}</h2>
            <form class="crm-form" method="post" action="{{ $quote->exists ? route('crm.quotes.update', $quote) : route('crm.quotes.store') }}">
                @csrf
                @if($quote->exists) @method('put') @endif
                <div class="crm-grid-2">
                    <label><span>Quote Number</span><input name="quote_number" value="{{ old('quote_number', $quote->quote_number ?? 'QT-'.now()->format('YmdHis')) }}" required></label>
                    <label><span>Status</span><input name="status" value="{{ old('status', $quote->status ?: 'draft') }}"></label>
                </div>
                <div class="crm-grid-2">
                    <label><span>Issue Date</span><input type="date" name="issue_date" value="{{ old('issue_date', optional($quote->issue_date)->format('Y-m-d') ?: now()->toDateString()) }}"></label>
                    <label><span>Valid Until</span><input type="date" name="valid_until" value="{{ old('valid_until', optional($quote->valid_until)->format('Y-m-d')) }}"></label>
                </div>
                <label><span>Company</span><select name="company_id"><option value="">None</option>@foreach($companies as $companyOption)<option value="{{ $companyOption->id }}" @selected((string) old('company_id', $quote->company_id) === (string) $companyOption->id)>{{ $companyOption->name }}</option>@endforeach</select></label>
                <label><span>Contact</span><select name="contact_id"><option value="">None</option>@foreach($contacts as $contactOption)<option value="{{ $contactOption->id }}" @selected((string) old('contact_id', $quote->contact_id) === (string) $contactOption->id)>{{ $contactOption->first_name }} {{ $contactOption->last_name }}</option>@endforeach</select></label>
                <label><span>Deal</span><select name="deal_id"><option value="">None</option>@foreach($deals as $dealOption)<option value="{{ $dealOption->id }}" @selected((string) old('deal_id', $quote->deal_id) === (string) $dealOption->id)>{{ $dealOption->name }}</option>@endforeach</select></label>
                <div class="crm-inline-items">
                    @php($items = old('items', $quote->exists ? $quote->items->toArray() : [['description' => '', 'quantity' => 1, 'unit_price' => 0, 'total' => 0], ['description' => '', 'quantity' => 1, 'unit_price' => 0, 'total' => 0]]))
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
                    <label><span>Subtotal</span><input type="number" step="0.01" name="subtotal" value="{{ old('subtotal', $quote->subtotal) }}"></label>
                    <label><span>Tax</span><input type="number" step="0.01" name="tax" value="{{ old('tax', $quote->tax) }}"></label>
                    <label><span>Total</span><input type="number" step="0.01" name="total" value="{{ old('total', $quote->total) }}"></label>
                </div>
                <label><span>Notes</span><textarea name="notes">{{ old('notes', $quote->notes) }}</textarea></label>
                <button class="crm-button-primary" type="submit">{{ $quote->exists ? 'Update Quote' : 'Create Quote' }}</button>
            </form>
        </section>

        <section class="crm-card">
            <h2>Quotes</h2>
            <div class="crm-table-wrap">
                <table>
                    <thead><tr><th>Quote</th><th>Company</th><th>Status</th><th>Total</th><th>Actions</th></tr></thead>
                    <tbody>
                    @foreach($quotes as $item)
                        <tr>
                            <td><strong>{{ $item->quote_number }}</strong><div class="muted">{{ optional($item->issue_date)->format('d M Y') }}</div></td>
                            <td>{{ $item->company?->name ?? 'No company' }}</td>
                            <td><span class="crm-badge">{{ $item->status }}</span></td>
                            <td>{{ number_format($item->total, 2) }}</td>
                            <td class="crm-actions">
                                <a class="crm-button crm-button-secondary" href="{{ route('crm.quotes.index', ['edit' => $item->id]) }}">Edit</a>
                                <form method="post" action="{{ route('crm.quotes.destroy', $item) }}">@csrf @method('delete')<button class="crm-button-danger" type="submit">Delete</button></form>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
            <div style="margin-top:1rem;">{{ $quotes->links() }}</div>
        </section>
    </div>
@endsection
