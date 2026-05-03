@extends('layouts.crm', ['heading' => 'Lead Management', 'subheading' => 'Capture, qualify, prioritize, and convert sales opportunities.'])

@section('content')
    <div class="crm-grid-2">
        <section class="crm-card">
            <h2>{{ $lead->exists ? 'Edit Lead' : 'Add Lead' }}</h2>
            <form class="crm-form" method="post" action="{{ $lead->exists ? route('crm.leads.update', $lead) : route('crm.leads.store') }}">
                @csrf
                @if($lead->exists) @method('put') @endif
                <label><span>Lead Title</span><input name="title" value="{{ old('title', $lead->title) }}" required></label>
                <div class="crm-grid-2">
                    <label><span>Source</span><input name="source" value="{{ old('source', $lead->source) }}"></label>
                    <label><span>Value</span><input type="number" step="0.01" name="value" value="{{ old('value', $lead->value) }}"></label>
                </div>
                <div class="crm-grid-2">
                    <label><span>Status</span><input name="status" value="{{ old('status', $lead->status ?: 'new') }}"></label>
                    <label><span>Priority</span><input name="priority" value="{{ old('priority', $lead->priority ?: 'medium') }}"></label>
                </div>
                <label><span>Company</span><select name="company_id"><option value="">None</option>@foreach($companies as $companyOption)<option value="{{ $companyOption->id }}" @selected((string) old('company_id', $lead->company_id) === (string) $companyOption->id)>{{ $companyOption->name }}</option>@endforeach</select></label>
                <label><span>Contact</span><select name="contact_id"><option value="">None</option>@foreach($contacts as $contactOption)<option value="{{ $contactOption->id }}" @selected((string) old('contact_id', $lead->contact_id) === (string) $contactOption->id)>{{ $contactOption->first_name }} {{ $contactOption->last_name }}</option>@endforeach</select></label>
                <label><span>Owner</span><select name="owner_id"><option value="">None</option>@foreach($users as $user)<option value="{{ $user->id }}" @selected((string) old('owner_id', $lead->owner_id) === (string) $user->id)>{{ $user->name }}</option>@endforeach</select></label>
                <label><span>Converted Deal</span><select name="converted_deal_id"><option value="">Not Converted</option>@foreach($deals as $dealOption)<option value="{{ $dealOption->id }}" @selected((string) old('converted_deal_id', $lead->converted_deal_id) === (string) $dealOption->id)>{{ $dealOption->name }}</option>@endforeach</select></label>
                <label><span>Notes</span><textarea name="notes">{{ old('notes', $lead->notes) }}</textarea></label>
                <button class="crm-button-primary" type="submit">{{ $lead->exists ? 'Update Lead' : 'Create Lead' }}</button>
            </form>
        </section>

        <section class="crm-card">
            <h2>Leads</h2>
            <div class="crm-table-wrap">
                <table>
                    <thead><tr><th>Lead</th><th>Owner</th><th>Status</th><th>Value</th><th>Actions</th></tr></thead>
                    <tbody>
                    @foreach($leads as $item)
                        <tr>
                            <td><strong>{{ $item->title }}</strong><div class="muted">{{ $item->company?->name ?? 'No company' }}</div></td>
                            <td>{{ $item->owner?->name ?? 'Unassigned' }}</td>
                            <td><span class="crm-badge">{{ $item->status }} • {{ $item->priority }}</span></td>
                            <td>{{ number_format($item->value, 2) }}</td>
                            <td class="crm-actions">
                                <a class="crm-button crm-button-secondary" href="{{ route('crm.leads.index', ['edit' => $item->id]) }}">Edit</a>
                                <form method="post" action="{{ route('crm.leads.destroy', $item) }}">@csrf @method('delete')<button class="crm-button-danger" type="submit">Delete</button></form>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
            <div style="margin-top:1rem;">{{ $leads->links() }}</div>
        </section>
    </div>
@endsection
