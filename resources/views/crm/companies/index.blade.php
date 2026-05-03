@extends('layouts.crm', ['heading' => 'Company Management', 'subheading' => 'Track accounts, ownership, and company-level context for every opportunity.'])

@section('content')
    <div class="crm-grid-2">
        <section class="crm-card">
            <h2>{{ $company->exists ? 'Edit Company' : 'Add Company' }}</h2>
            <form class="crm-form" method="post" action="{{ $company->exists ? route('crm.companies.update', $company) : route('crm.companies.store') }}">
                @csrf
                @if($company->exists) @method('put') @endif
                <label><span>Name</span><input name="name" value="{{ old('name', $company->name) }}" required></label>
                <label><span>Industry</span><input name="industry" value="{{ old('industry', $company->industry) }}"></label>
                <label><span>Website</span><input name="website" value="{{ old('website', $company->website) }}"></label>
                <label><span>Email</span><input type="email" name="email" value="{{ old('email', $company->email) }}"></label>
                <label><span>Phone</span><input name="phone" value="{{ old('phone', $company->phone) }}"></label>
                <label><span>Address</span><input name="address" value="{{ old('address', $company->address) }}"></label>
                <label><span>Owner</span><select name="owner_id"><option value="">None</option>@foreach($users as $user)<option value="{{ $user->id }}" @selected((string) old('owner_id', $company->owner_id) === (string) $user->id)>{{ $user->name }}</option>@endforeach</select></label>
                <label><span>Notes</span><textarea name="notes">{{ old('notes', $company->notes) }}</textarea></label>
                <div class="crm-actions">
                    <button class="crm-button-primary" type="submit">{{ $company->exists ? 'Update Company' : 'Create Company' }}</button>
                    @if($company->exists)<a class="crm-button crm-button-secondary" href="{{ route('crm.companies.index') }}">Cancel</a>@endif
                </div>
            </form>
        </section>

        <section class="crm-card">
            <h2>Companies</h2>
            <div class="crm-table-wrap">
                <table>
                    <thead><tr><th>Name</th><th>Owner</th><th>Industry</th><th>Actions</th></tr></thead>
                    <tbody>
                    @foreach($companies as $item)
                        <tr>
                            <td><strong>{{ $item->name }}</strong><div class="muted">{{ $item->email }}</div></td>
                            <td>{{ $item->owner?->name ?? 'Unassigned' }}</td>
                            <td>{{ $item->industry ?: 'N/A' }}</td>
                            <td class="crm-actions">
                                <a class="crm-button crm-button-secondary" href="{{ route('crm.companies.index', ['edit' => $item->id]) }}">Edit</a>
                                <form method="post" action="{{ route('crm.companies.destroy', $item) }}">@csrf @method('delete')<button class="crm-button-danger" type="submit">Delete</button></form>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
            <div style="margin-top:1rem;">{{ $companies->links() }}</div>
        </section>
    </div>
@endsection
