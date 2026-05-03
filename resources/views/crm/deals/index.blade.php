@extends('layouts.crm', ['heading' => 'Deal Pipeline', 'subheading' => 'Customizable pipelines, stages, forecast values, and expected close dates.'])

@section('content')
    <div class="crm-grid-2">
        <section class="crm-card">
            <h2>{{ $deal->exists ? 'Edit Deal' : 'Add Deal' }}</h2>
            <form class="crm-form" method="post" action="{{ $deal->exists ? route('crm.deals.update', $deal) : route('crm.deals.store') }}">
                @csrf
                @if($deal->exists) @method('put') @endif
                <label><span>Deal Name</span><input name="name" value="{{ old('name', $deal->name) }}" required></label>
                <div class="crm-grid-2">
                    <label><span>Value</span><input type="number" step="0.01" name="value" value="{{ old('value', $deal->value) }}"></label>
                    <label><span>Status</span><input name="status" value="{{ old('status', $deal->status ?: 'open') }}"></label>
                </div>
                <label><span>Company</span><select name="company_id"><option value="">None</option>@foreach($companies as $companyOption)<option value="{{ $companyOption->id }}" @selected((string) old('company_id', $deal->company_id) === (string) $companyOption->id)>{{ $companyOption->name }}</option>@endforeach</select></label>
                <label><span>Contact</span><select name="contact_id"><option value="">None</option>@foreach($contacts as $contactOption)<option value="{{ $contactOption->id }}" @selected((string) old('contact_id', $deal->contact_id) === (string) $contactOption->id)>{{ $contactOption->first_name }} {{ $contactOption->last_name }}</option>@endforeach</select></label>
                <div class="crm-grid-2">
                    <label><span>Pipeline</span><select name="pipeline_id"><option value="">None</option>@foreach($pipelines as $pipeline)<option value="{{ $pipeline->id }}" @selected((string) old('pipeline_id', $deal->pipeline_id) === (string) $pipeline->id)>{{ $pipeline->name }}</option>@endforeach</select></label>
                    <label><span>Stage</span><select name="stage_id"><option value="">None</option>@foreach($stages as $stage)<option value="{{ $stage->id }}" @selected((string) old('stage_id', $deal->stage_id) === (string) $stage->id)>{{ $stage->pipeline?->name }} • {{ $stage->name }}</option>@endforeach</select></label>
                </div>
                <label><span>Owner</span><select name="owner_id"><option value="">None</option>@foreach($users as $user)<option value="{{ $user->id }}" @selected((string) old('owner_id', $deal->owner_id) === (string) $user->id)>{{ $user->name }}</option>@endforeach</select></label>
                <label><span>Expected Close</span><input type="date" name="expected_close_at" value="{{ old('expected_close_at', optional($deal->expected_close_at)->format('Y-m-d')) }}"></label>
                <label><span>Notes</span><textarea name="notes">{{ old('notes', $deal->notes) }}</textarea></label>
                <button class="crm-button-primary" type="submit">{{ $deal->exists ? 'Update Deal' : 'Create Deal' }}</button>
            </form>
        </section>

        <section class="crm-card">
            <h2>Deals</h2>
            <div class="crm-table-wrap">
                <table>
                    <thead><tr><th>Deal</th><th>Stage</th><th>Owner</th><th>Value</th><th>Actions</th></tr></thead>
                    <tbody>
                    @foreach($deals as $item)
                        <tr>
                            <td><strong>{{ $item->name }}</strong><div class="muted">{{ $item->company?->name ?? 'No company' }}</div></td>
                            <td><span class="crm-badge">{{ $item->stage?->name ?? 'No stage' }}</span></td>
                            <td>{{ $item->owner?->name ?? 'Unassigned' }}</td>
                            <td>{{ number_format($item->value, 2) }}</td>
                            <td class="crm-actions">
                                <a class="crm-button crm-button-secondary" href="{{ route('crm.deals.index', ['edit' => $item->id]) }}">Edit</a>
                                <form method="post" action="{{ route('crm.deals.destroy', $item) }}">@csrf @method('delete')<button class="crm-button-danger" type="submit">Delete</button></form>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
            <div style="margin-top:1rem;">{{ $deals->links() }}</div>
        </section>
    </div>
@endsection
