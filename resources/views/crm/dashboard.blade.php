@extends('layouts.crm', ['heading' => 'CRM Dashboard', 'subheading' => 'Reporting, analytics, and pipeline visibility for the full sales organization.'])

@section('content')
    <section class="crm-stats">
        <article class="crm-card crm-stat"><span class="muted">Leads</span><strong>{{ $metrics['leads'] }}</strong></article>
        <article class="crm-card crm-stat"><span class="muted">Contacts</span><strong>{{ $metrics['contacts'] }}</strong></article>
        <article class="crm-card crm-stat"><span class="muted">Companies</span><strong>{{ $metrics['companies'] }}</strong></article>
        <article class="crm-card crm-stat"><span class="muted">Open Deal Value</span><strong>{{ number_format($metrics['open_deal_value']) }}</strong></article>
        <article class="crm-card crm-stat"><span class="muted">Open Tasks</span><strong>{{ $metrics['tasks_due'] }}</strong></article>
    </section>

    <div class="crm-grid-2" style="margin-top:1rem;">
        <section class="crm-card">
            <h2>Pipeline Performance</h2>
            @foreach($pipeline as $stage)
                <div style="margin-bottom:1rem;">
                    <div style="display:flex;justify-content:space-between;gap:1rem;">
                        <strong>{{ $stage->name }}</strong>
                        <span>{{ $stage->deals_count }} deals</span>
                    </div>
                    <div class="crm-chart-bar"><span style="width: {{ min(100, max(8, $stage->probability)) }}%; background: {{ $stage->color }}"></span></div>
                </div>
            @endforeach
        </section>

        <section class="crm-card">
            <h2>Recent Activity</h2>
            @forelse($recentActivities as $activity)
                <div style="padding:.8rem 0;border-bottom:1px solid var(--line);">
                    <strong>{{ ucfirst(str_replace('_', ' ', $activity->activity_type)) }}</strong>
                    <div>{{ $activity->description }}</div>
                    <small class="muted">{{ $activity->user?->name ?? 'System' }} • {{ $activity->created_at->diffForHumans() }}</small>
                </div>
            @empty
                <p class="muted">No recent activities logged yet.</p>
            @endforelse
        </section>
    </div>
@endsection
