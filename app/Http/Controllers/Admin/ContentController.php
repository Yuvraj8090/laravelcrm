<?php

namespace App\Http\Controllers\Admin;

use App\Cms\Content\Models\Content;
use App\Cms\Content\Models\Media;
use App\Cms\Content\Models\Taxonomy;
use App\Http\Requests\BulkContentActionRequest;
use App\Http\Requests\SaveContentRequest;
use App\Support\Services\ActivityLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class ContentController extends AdminController
{
    public function __construct(
        protected ActivityLogger $activity
    ) {
    }

    public function index(Request $request): View
    {
        $website = $this->currentWebsite($request);
        $type = $request->string('type', 'page')->toString();
        $status = $request->string('status')->toString();

        $contents = Content::query()
            ->with(['author', 'parent', 'taxonomies'])
            ->when($type, fn ($query) => $query->where('type', $type))
            ->when($status, fn ($query) => $query->where('status', $status))
            ->latest('updated_at')
            ->paginate(15)
            ->withQueryString();

        $categories = Taxonomy::query()->where('type', 'category')->orderBy('name')->get();

        return view('admin.contents.index', compact('website', 'contents', 'type', 'categories'));
    }

    public function create(Request $request): View
    {
        $this->currentWebsite($request);

        return view('admin.contents.form', [
            'content' => new Content(['type' => $request->string('type', 'page')->toString(), 'status' => 'draft']),
            'parents' => Content::query()->orderBy('title')->get(),
            'categories' => Taxonomy::query()->where('type', 'category')->orderBy('name')->get(),
            'tags' => Taxonomy::query()->where('type', 'tag')->orderBy('name')->get(),
            'mediaLibrary' => Media::query()->latest()->limit(24)->get(),
        ]);
    }

    public function store(SaveContentRequest $request): RedirectResponse
    {
        $website = $this->currentWebsite($request);

        $content = Content::query()->create([
            ...$request->safe()->except(['category_ids', 'tag_ids']),
            'website_id' => $website->id,
            'author_id' => $request->user()->id,
        ]);

        $content->taxonomies()->sync(array_merge(
            $request->input('category_ids', []),
            $request->input('tag_ids', [])
        ));

        Cache::forget("dashboard:stats:{$website->id}");
        $this->activity->log('content.created', $content);

        return redirect()->route('admin.contents.index', ['type' => $content->type])->with('status', 'Content created.');
    }

    public function edit(Request $request, Content $content): View
    {
        $this->currentWebsite($request);
        $content->load('taxonomies', 'sections');

        return view('admin.contents.form', [
            'content' => $content,
            'parents' => Content::query()->whereKeyNot($content->id)->orderBy('title')->get(),
            'categories' => Taxonomy::query()->where('type', 'category')->orderBy('name')->get(),
            'tags' => Taxonomy::query()->where('type', 'tag')->orderBy('name')->get(),
            'mediaLibrary' => Media::query()->latest()->limit(24)->get(),
        ]);
    }

    public function update(SaveContentRequest $request, Content $content): RedirectResponse
    {
        $website = $this->currentWebsite($request);

        $content->update($request->safe()->except(['category_ids', 'tag_ids']));
        $content->taxonomies()->sync(array_merge(
            $request->input('category_ids', []),
            $request->input('tag_ids', [])
        ));

        Cache::forget("dashboard:stats:{$website->id}");
        $this->activity->log('content.updated', $content);

        return redirect()->route('admin.contents.edit', $content)->with('status', 'Content updated.');
    }

    public function destroy(Request $request, Content $content): RedirectResponse
    {
        $website = $this->currentWebsite($request);
        $content->delete();
        Cache::forget("dashboard:stats:{$website->id}");
        $this->activity->log('content.deleted', $content);

        return back()->with('status', 'Content moved to trash.');
    }

    public function restore(Request $request, int $content): RedirectResponse
    {
        $website = $this->currentWebsite($request);
        $model = Content::withTrashed()->findOrFail($content);
        $model->restore();
        Cache::forget("dashboard:stats:{$website->id}");
        $this->activity->log('content.restored', $model);

        return back()->with('status', 'Content restored.');
    }

    public function bulk(BulkContentActionRequest $request): RedirectResponse
    {
        $website = $this->currentWebsite($request);

        $query = Content::withTrashed()->whereIn('id', $request->validated('content_ids'));

        $action = $request->validated('action');
        $selection = $query->get();

        match ($action) {
            'publish' => $query->update(['status' => 'published']),
            'draft' => $query->update(['status' => 'draft']),
            'delete' => $selection->each->delete(),
            'restore' => $query->onlyTrashed()->restore(),
            'category' => $selection->each(function (Content $content) use ($request) {
                $content->taxonomies()->syncWithoutDetaching([$request->integer('taxonomy_id')]);
            }),
        };

        Cache::forget("dashboard:stats:{$website->id}");
        $this->activity->log('content.bulk', null, $request->validated());

        return back()->with('status', 'Bulk action completed.');
    }
}
