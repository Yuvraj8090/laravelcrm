@extends('layouts.admin', ['heading' => 'Widgets', 'subheading' => 'Configure sidebar, footer, and custom widget areas.'])

@section('content')
    <div class="grid-2">
        <section class="card">
            <h2 style="margin-top:0;">Add Widget</h2>
            <form method="post" action="{{ route('admin.widgets.store') }}">
                @csrf
                <div class="field"><label>Name</label><input name="name"></div>
                <div class="field"><label>Area</label><input name="area" value="sidebar"></div>
                <div class="field"><label>Type</label><input name="type" value="text"></div>
                <div class="field"><label>Title Setting</label><input name="settings[title]"></div>
                <div class="field"><label>Body Setting</label><textarea name="settings[body]"></textarea></div>
                <label><input type="checkbox" name="is_active" value="1" style="width:auto;"> Active</label>
                <div style="margin-top:1rem;"><button type="submit">Save widget</button></div>
            </form>
        </section>
        <section class="stack">
            @foreach($widgets as $widget)
                <article class="card">
                    <div class="split">
                        <div>
                            <strong>{{ $widget->name }}</strong>
                            <div class="muted">{{ $widget->type }} in {{ $widget->area }}</div>
                        </div>
                        <form method="post" action="{{ route('admin.widgets.destroy', $widget) }}">@csrf @method('delete')<button class="danger">Delete</button></form>
                    </div>
                </article>
            @endforeach
        </section>
    </div>
@endsection
