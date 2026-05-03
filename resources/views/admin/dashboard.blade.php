@extends('layouts.admin', ['heading' => 'Dashboard', 'subheading' => 'Overview of content, publishing, and recent admin activity.'])

@section('content')
    <div class="stats">
        @foreach($stats as $label => $value)
            <div class="card stat">
                <span class="muted">{{ ucfirst($label) }}</span>
                <strong>{{ $value }}</strong>
            </div>
        @endforeach
    </div>

    <div class="grid-2" style="margin-top: 1rem;">
        <section class="card">
            <div class="split">
                <h2 style="margin:0;">Quick Actions</h2>
            </div>
            <div class="actions" style="margin-top: 1rem;">
                <a class="btn" href="{{ route('admin.contents.create', ['type' => 'page']) }}">New page</a>
                <a class="btn secondary" href="{{ route('admin.contents.create', ['type' => 'post']) }}">New post</a>
                <a class="btn secondary" href="{{ route('admin.media.index') }}">Upload media</a>
            </div>
        </section>

        <section class="card">
            <h2 style="margin-top:0;">Recent Activity</h2>
            <div class="stack">
                @forelse($activities as $activity)
                    <div>
                        <strong>{{ $activity->action }}</strong>
                        <div class="muted">{{ $activity->created_at->diffForHumans() }}</div>
                    </div>
                @empty
                    <div class="muted">No activity logged yet.</div>
                @endforelse
            </div>
        </section>
    </div>
@endsection
