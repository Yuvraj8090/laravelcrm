<?php

namespace App\Http\Controllers\Admin;

use App\Cms\Content\Models\Taxonomy;
use App\Http\Requests\SaveTaxonomyRequest;
use App\Support\Services\ActivityLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TaxonomyController extends AdminController
{
    public function __construct(
        protected ActivityLogger $activity
    ) {
    }

    public function index(Request $request): View
    {
        $this->currentWebsite($request);
        $taxonomies = Taxonomy::query()->orderBy('type')->orderBy('name')->paginate(20);

        return view('admin.taxonomies.index', compact('taxonomies'));
    }

    public function store(SaveTaxonomyRequest $request): RedirectResponse
    {
        $taxonomy = Taxonomy::query()->create($request->validated());
        $this->activity->log('taxonomy.created', $taxonomy);

        return back()->with('status', 'Taxonomy created.');
    }

    public function update(SaveTaxonomyRequest $request, Taxonomy $taxonomy): RedirectResponse
    {
        $taxonomy->update($request->validated());
        $this->activity->log('taxonomy.updated', $taxonomy);

        return back()->with('status', 'Taxonomy updated.');
    }

    public function destroy(Taxonomy $taxonomy): RedirectResponse
    {
        $taxonomy->delete();
        $this->activity->log('taxonomy.deleted', $taxonomy);

        return back()->with('status', 'Taxonomy deleted.');
    }
}
