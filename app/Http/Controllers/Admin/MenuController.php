<?php

namespace App\Http\Controllers\Admin;

use App\Cms\Content\Models\Menu;
use App\Http\Requests\SaveMenuRequest;
use App\Support\Services\ActivityLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MenuController extends AdminController
{
    public function __construct(
        protected ActivityLogger $activity
    ) {
    }

    public function index(Request $request): View
    {
        $this->currentWebsite($request);
        $menus = Menu::query()->with('items')->latest()->get();

        return view('admin.menus.index', compact('menus'));
    }

    public function store(SaveMenuRequest $request): RedirectResponse
    {
        $menu = Menu::query()->create($request->safe()->except('items'));

        foreach ($request->validated('items', []) as $index => $item) {
            $menu->items()->create([
                'title' => $item['title'],
                'url' => $item['url'] ?? '#',
                'sort_order' => $item['sort_order'] ?? $index,
            ]);
        }

        $this->activity->log('menu.created', $menu);

        return back()->with('status', 'Menu saved.');
    }

    public function update(SaveMenuRequest $request, Menu $menu): RedirectResponse
    {
        $menu->update($request->safe()->except('items'));
        $menu->items()->delete();

        foreach ($request->validated('items', []) as $index => $item) {
            $menu->items()->create([
                'title' => $item['title'],
                'url' => $item['url'] ?? '#',
                'sort_order' => $item['sort_order'] ?? $index,
            ]);
        }

        $this->activity->log('menu.updated', $menu);

        return back()->with('status', 'Menu updated.');
    }

    public function destroy(Menu $menu): RedirectResponse
    {
        $menu->delete();
        $this->activity->log('menu.deleted', $menu);

        return back()->with('status', 'Menu deleted.');
    }
}
