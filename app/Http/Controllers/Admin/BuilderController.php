<?php

namespace App\Http\Controllers\Admin;

use App\Cms\Content\Models\Content;
use App\Cms\Content\Models\PageSection;
use App\Http\Requests\SaveBuilderRequest;
use App\Support\Services\ActivityLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BuilderController extends AdminController
{
    public function __construct(
        protected ActivityLogger $activity
    ) {
    }

    public function edit(Request $request, Content $content): View
    {
        $this->currentWebsite($request);
        $content->load('sections');

        $sectionTemplates = [
            'hero' => ['title' => 'Hero', 'description' => 'Big intro with call to action'],
            'content' => ['title' => 'Content Block', 'description' => 'Rich text section'],
            'gallery' => ['title' => 'Gallery', 'description' => 'Image showcase'],
            'contact' => ['title' => 'Contact Form', 'description' => 'Lead capture section'],
            'footer' => ['title' => 'Footer', 'description' => 'Closing section with links'],
        ];

        $reusableSections = PageSection::query()->where('is_reusable', true)->orderBy('name')->get();

        return view('admin.builder.edit', compact('content', 'sectionTemplates', 'reusableSections'));
    }

    public function update(SaveBuilderRequest $request, Content $content): RedirectResponse
    {
        $this->currentWebsite($request);
        $content->sections()->delete();

        foreach ($request->validated('sections') as $index => $section) {
            $content->sections()->create([
                'website_id' => $content->website_id,
                'name' => $section['name'] ?? null,
                'type' => $section['type'],
                'sort_order' => $section['sort_order'] ?? $index,
                'settings' => $section['settings'] ?? [],
                'is_reusable' => (bool) ($section['is_reusable'] ?? false),
            ]);
        }

        $content->update(['builder_data' => $request->validated('sections')]);
        $this->activity->log('builder.updated', $content);

        return back()->with('status', 'Page builder updated.');
    }
}
