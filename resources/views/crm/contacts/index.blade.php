@extends('layouts.crm', ['heading' => 'Contact Management', 'subheading' => 'Detailed profiles, ownership, contact status, and account relationships.'])

@section('content')
    <div class="crm-grid-2">
        <section class="crm-card">
            <h2>{{ $contact->exists ? 'Edit Contact' : 'Add Contact' }}</h2>
            <form class="crm-form" method="post" action="{{ $contact->exists ? route('crm.contacts.update', $contact) : route('crm.contacts.store') }}">
                @csrf
                @if($contact->exists) @method('put') @endif
                <div class="crm-grid-2">
                    <label><span>First Name</span><input name="first_name" value="{{ old('first_name', $contact->first_name) }}" required></label>
                    <label><span>Last Name</span><input name="last_name" value="{{ old('last_name', $contact->last_name) }}" required></label>
                </div>
                <label><span>Company</span><select name="company_id"><option value="">None</option>@foreach($companies as $companyOption)<option value="{{ $companyOption->id }}" @selected((string) old('company_id', $contact->company_id) === (string) $companyOption->id)>{{ $companyOption->name }}</option>@endforeach</select></label>
                <div class="crm-grid-2">
                    <label><span>Email</span><input type="email" name="email" value="{{ old('email', $contact->email) }}"></label>
                    <label><span>Phone</span><input name="phone" value="{{ old('phone', $contact->phone) }}"></label>
                </div>
                <div class="crm-grid-2">
                    <label><span>Job Title</span><input name="title" value="{{ old('title', $contact->title) }}"></label>
                    <label><span>Status</span><input name="status" value="{{ old('status', $contact->status ?: 'active') }}"></label>
                </div>
                <label><span>Owner</span><select name="owner_id"><option value="">None</option>@foreach($users as $user)<option value="{{ $user->id }}" @selected((string) old('owner_id', $contact->owner_id) === (string) $user->id)>{{ $user->name }}</option>@endforeach</select></label>
                <label><span>Last Contacted At</span><input type="datetime-local" name="last_contacted_at" value="{{ old('last_contacted_at', optional($contact->last_contacted_at)->format('Y-m-d\TH:i')) }}"></label>
                <label><span>Notes</span><textarea name="notes">{{ old('notes', $contact->notes) }}</textarea></label>
                <button class="crm-button-primary" type="submit">{{ $contact->exists ? 'Update Contact' : 'Create Contact' }}</button>
            </form>
        </section>

        <section class="crm-card">
            <h2>Contacts</h2>
            <div class="crm-table-wrap">
                <table>
                    <thead><tr><th>Name</th><th>Company</th><th>Owner</th><th>Status</th><th>Actions</th></tr></thead>
                    <tbody>
                    @foreach($contacts as $item)
                        <tr>
                            <td><strong>{{ $item->first_name }} {{ $item->last_name }}</strong><div class="muted">{{ $item->email }}</div></td>
                            <td>{{ $item->company?->name ?? 'Independent' }}</td>
                            <td>{{ $item->owner?->name ?? 'Unassigned' }}</td>
                            <td><span class="crm-badge">{{ $item->status }}</span></td>
                            <td class="crm-actions">
                                <a class="crm-button crm-button-secondary" href="{{ route('crm.contacts.index', ['edit' => $item->id]) }}">Edit</a>
                                <form method="post" action="{{ route('crm.contacts.destroy', $item) }}">@csrf @method('delete')<button class="crm-button-danger" type="submit">Delete</button></form>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
            <div style="margin-top:1rem;">{{ $contacts->links() }}</div>
        </section>
    </div>
@endsection
