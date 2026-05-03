<?php

namespace App\Http\Controllers\Admin;

use App\Cms\Content\Models\Widget;
use App\Http\Requests\SaveWidgetRequest;
use App\Support\Services\ActivityLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WidgetController extends AdminController
{
    public function __construct(
        protected ActivityLogger $activity
    ) {
    }

    public function index(Request $request): View
    {
        $this->currentWebsite($request);
        $widgets = Widget::query()->latest()->get();

        return view('admin.widgets.index', compact('widgets'));
    }

    public function store(SaveWidgetRequest $request): RedirectResponse
    {
        $widget = Widget::query()->create([
            ...$request->safe()->all(),
            'is_active' => $request->boolean('is_active'),
        ]);

        $this->activity->log('widget.created', $widget);

        return back()->with('status', 'Widget created.');
    }

    public function update(SaveWidgetRequest $request, Widget $widget): RedirectResponse
    {
        $widget->update([
            ...$request->safe()->all(),
            'is_active' => $request->boolean('is_active'),
        ]);

        $this->activity->log('widget.updated', $widget);

        return back()->with('status', 'Widget updated.');
    }

    public function destroy(Widget $widget): RedirectResponse
    {
        $widget->delete();
        $this->activity->log('widget.deleted', $widget);

        return back()->with('status', 'Widget deleted.');
    }
}
