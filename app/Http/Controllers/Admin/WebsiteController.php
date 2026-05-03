<?php

namespace App\Http\Controllers\Admin;

use App\Core\Website\Models\Website;
use App\Http\Requests\SaveWebsiteRequest;
use App\Support\Services\ActivityLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WebsiteController extends AdminController
{
    public function __construct(
        protected ActivityLogger $activity
    ) {
    }

    public function index(Request $request): View
    {
        $this->currentWebsite($request);

        $websites = Website::query()->withTrashed()->latest()->paginate(12);

        return view('admin.websites.index', compact('websites'));
    }

    public function create(Request $request): View
    {
        $this->currentWebsite($request);

        return view('admin.websites.form', ['website' => new Website()]);
    }

    public function store(SaveWebsiteRequest $request): RedirectResponse
    {
        $website = Website::query()->create($request->validated());
        $website->domains()->create([
            'domain' => $website->primary_domain ?: $website->slug.'.local',
            'is_primary' => true,
        ]);

        $this->activity->log('website.created', $website);

        return redirect()->route('admin.websites.index')->with('status', 'Website created.');
    }

    public function edit(Request $request, Website $website): View
    {
        $this->currentWebsite($request);

        return view('admin.websites.form', compact('website'));
    }

    public function update(SaveWebsiteRequest $request, Website $website): RedirectResponse
    {
        $website->update($request->validated());
        $website->domains()->updateOrCreate(
            ['website_id' => $website->id, 'is_primary' => true],
            ['domain' => $website->primary_domain ?: $website->slug.'.local']
        );

        $this->activity->log('website.updated', $website);

        return redirect()->route('admin.websites.index')->with('status', 'Website updated.');
    }

    public function destroy(Website $website): RedirectResponse
    {
        $website->delete();
        $this->activity->log('website.deleted', $website);

        return back()->with('status', 'Website moved to trash.');
    }

    public function restore(int $website): RedirectResponse
    {
        $model = Website::withTrashed()->findOrFail($website);
        $model->restore();
        $this->activity->log('website.restored', $model);

        return back()->with('status', 'Website restored.');
    }
}
