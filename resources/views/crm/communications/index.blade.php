@extends('layouts.crm', ['heading' => 'Email & Communication Log', 'subheading' => 'Track communications without requiring paid email providers or external APIs.'])

@section('content')
    <div class="crm-grid-2">
        <section class="crm-card">
            <h2>{{ $email->exists ? 'Edit Communication' : 'Log Communication' }}</h2>
            <form class="crm-form" method="post" action="{{ $email->exists ? route('crm.communications.update', $email) : route('crm.communications.store') }}">
                @csrf
                @if($email->exists) @method('put') @endif
                <label><span>Subject</span><input name="subject" value="{{ old('subject', $email->subject) }}" required></label>
                <div class="crm-grid-2">
                    <label><span>Direction</span><input name="direction" value="{{ old('direction', $email->direction ?: 'outbound') }}"></label>
                    <label><span>Status</span><input name="status" value="{{ old('status', $email->status ?: 'logged') }}"></label>
                </div>
                <label><span>Company</span><select name="company_id"><option value="">None</option>@foreach($companies as $companyOption)<option value="{{ $companyOption->id }}" @selected((string) old('company_id', $email->company_id) === (string) $companyOption->id)>{{ $companyOption->name }}</option>@endforeach</select></label>
                <label><span>Contact</span><select name="contact_id"><option value="">None</option>@foreach($contacts as $contactOption)<option value="{{ $contactOption->id }}" @selected((string) old('contact_id', $email->contact_id) === (string) $contactOption->id)>{{ $contactOption->first_name }} {{ $contactOption->last_name }}</option>@endforeach</select></label>
                <label><span>Deal</span><select name="deal_id"><option value="">None</option>@foreach($deals as $dealOption)<option value="{{ $dealOption->id }}" @selected((string) old('deal_id', $email->deal_id) === (string) $dealOption->id)>{{ $dealOption->name }}</option>@endforeach</select></label>
                <label><span>Sent At</span><input type="datetime-local" name="sent_at" value="{{ old('sent_at', optional($email->sent_at)->format('Y-m-d\TH:i')) }}"></label>
                <label><span>Body</span><textarea name="body">{{ old('body', $email->body) }}</textarea></label>
                <button class="crm-button-primary" type="submit">{{ $email->exists ? 'Update Communication' : 'Save Communication' }}</button>
            </form>
        </section>

        <section class="crm-card">
            <h2>Communication History</h2>
            <div class="crm-table-wrap">
                <table>
                    <thead><tr><th>Subject</th><th>Related</th><th>Direction</th><th>Sent</th><th>Actions</th></tr></thead>
                    <tbody>
                    @foreach($emails as $item)
                        <tr>
                            <td><strong>{{ $item->subject }}</strong><div class="muted">{{ \Illuminate\Support\Str::limit($item->body, 60) }}</div></td>
                            <td>{{ $item->contact?->first_name }} {{ $item->contact?->last_name }}<div class="muted">{{ $item->company?->name }}</div></td>
                            <td><span class="crm-badge">{{ $item->direction }}</span></td>
                            <td>{{ optional($item->sent_at)->format('d M Y H:i') ?: 'Not set' }}</td>
                            <td class="crm-actions">
                                <a class="crm-button crm-button-secondary" href="{{ route('crm.communications.index', ['edit' => $item->id]) }}">Edit</a>
                                <form method="post" action="{{ route('crm.communications.destroy', $item) }}">@csrf @method('delete')<button class="crm-button-danger" type="submit">Delete</button></form>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
            <div style="margin-top:1rem;">{{ $emails->links() }}</div>
        </section>
    </div>
@endsection
