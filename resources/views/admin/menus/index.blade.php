@extends('layouts.admin', ['heading' => 'Menus', 'subheading' => 'Manage navigation structures for headers, footers, and other menu locations.'])

@section('content')
    <div class="grid-2">
        <section class="card">
            <h2 style="margin-top:0;">Create Menu</h2>
            <form method="post" action="{{ route('admin.menus.store') }}">
                @csrf
                <div class="field"><label>Name</label><input name="name"></div>
                <div class="field"><label>Location</label><input name="location" placeholder="header"></div>
                <div class="field"><label>First Item Title</label><input name="items[0][title]" placeholder="Home"></div>
                <div class="field"><label>First Item URL</label><input name="items[0][url]" placeholder="/"></div>
                <button type="submit">Save menu</button>
            </form>
        </section>
        <section class="stack">
            @foreach($menus as $menu)
                <article class="card">
                    <div class="split">
                        <div>
                            <strong>{{ $menu->name }}</strong>
                            <div class="muted">{{ $menu->location ?: 'No location' }}</div>
                        </div>
                        <form method="post" action="{{ route('admin.menus.destroy', $menu) }}">@csrf @method('delete')<button class="danger">Delete</button></form>
                    </div>
                    <div class="chip-list" style="margin-top:.8rem;">
                        @foreach($menu->items as $item)
                            <span class="chip">{{ $item->title }} → {{ $item->url }}</span>
                        @endforeach
                    </div>
                </article>
            @endforeach
        </section>
    </div>
@endsection
