@extends('layouts.crm', ['heading' => 'Sales Pipelines', 'subheading' => 'Create custom sales pipelines and configure stage probability progression.'])

@section('content')
    <div class="crm-grid-2">
        <section class="crm-card">
            <h2>{{ $pipeline->exists ? 'Edit Pipeline' : 'Add Pipeline' }}</h2>
            <form class="crm-form" method="post" action="{{ $pipeline->exists ? route('crm.pipelines.update', $pipeline) : route('crm.pipelines.store') }}">
                @csrf
                @if($pipeline->exists) @method('put') @endif
                <label><span>Pipeline Name</span><input name="name" value="{{ old('name', $pipeline->name) }}" required></label>
                <label><span><input type="checkbox" name="is_default" value="1" @checked(old('is_default', $pipeline->is_default))> Default Pipeline</span></label>
                <div class="crm-inline-items">
                    @php($stageSource = old('stages', $pipeline->exists ? $pipeline->stages->toArray() : [['name' => 'Qualified', 'position' => 1, 'probability' => 25, 'color' => '#2563eb'], ['name' => 'Proposal', 'position' => 2, 'probability' => 60, 'color' => '#0d9488'], ['name' => 'Closed Won', 'position' => 3, 'probability' => 100, 'color' => '#16a34a']]))
                    @foreach($stageSource as $index => $stage)
                        <div class="crm-inline-item">
                            <input name="stages[{{ $index }}][name]" placeholder="Stage name" value="{{ $stage['name'] ?? '' }}">
                            <input name="stages[{{ $index }}][position]" type="number" placeholder="Position" value="{{ $stage['position'] ?? $loop->iteration }}">
                            <input name="stages[{{ $index }}][probability]" type="number" placeholder="Probability" value="{{ $stage['probability'] ?? 0 }}">
                            <input name="stages[{{ $index }}][color]" placeholder="#2563eb" value="{{ $stage['color'] ?? '#2563eb' }}">
                        </div>
                    @endforeach
                </div>
                <button class="crm-button-primary" type="submit">{{ $pipeline->exists ? 'Update Pipeline' : 'Create Pipeline' }}</button>
            </form>
        </section>

        <section class="crm-card">
            <h2>Pipeline Library</h2>
            @foreach($pipelines as $item)
                <div style="padding:1rem 0;border-bottom:1px solid var(--line);">
                    <div style="display:flex;justify-content:space-between;gap:1rem;">
                        <div>
                            <strong>{{ $item->name }}</strong>
                            @if($item->is_default)<span class="crm-badge">Default</span>@endif
                        </div>
                        <div class="crm-actions">
                            <a class="crm-button crm-button-secondary" href="{{ route('crm.pipelines.index', ['edit' => $item->id]) }}">Edit</a>
                            <form method="post" action="{{ route('crm.pipelines.destroy', $item) }}">@csrf @method('delete')<button class="crm-button-danger" type="submit">Delete</button></form>
                        </div>
                    </div>
                    @foreach($item->stages as $stage)
                        <div style="margin-top:.65rem;">
                            <strong>{{ $stage->position }}. {{ $stage->name }}</strong>
                            <div class="crm-chart-bar"><span style="width: {{ max(8, $stage->probability) }}%; background: {{ $stage->color }}"></span></div>
                        </div>
                    @endforeach
                </div>
            @endforeach
        </section>
    </div>
@endsection
