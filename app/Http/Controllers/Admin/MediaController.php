<?php

namespace App\Http\Controllers\Admin;

use App\Cms\Content\Models\Media;
use App\Http\Requests\StoreMediaRequest;
use App\Support\Services\ActivityLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class MediaController extends AdminController
{
    public function __construct(
        protected ActivityLogger $activity
    ) {
    }

    public function index(Request $request): View
    {
        $this->currentWebsite($request);
        $media = Media::query()->latest()->paginate(24);

        return view('admin.media.index', compact('media'));
    }

    public function store(StoreMediaRequest $request): RedirectResponse
    {
        $website = $this->currentWebsite($request);
        $file = $request->file('file');
        $path = $file->store("websites/{$website->id}", 'public');

        $media = Media::query()->create([
            'website_id' => $website->id,
            'uploaded_by' => $request->user()->id,
            'disk' => 'public',
            'path' => $path,
            'filename' => $file->getClientOriginalName(),
            'mime_type' => $file->getMimeType(),
            'size' => $file->getSize(),
            'alt_text' => $request->string('alt_text')->toString(),
            'meta' => ['url' => Storage::disk('public')->url($path)],
        ]);

        $this->activity->log('media.created', $media);

        return back()->with('status', 'File uploaded.');
    }

    public function destroy(Request $request, Media $media): RedirectResponse
    {
        $this->currentWebsite($request);
        Storage::disk($media->disk)->delete($media->path);
        $media->delete();
        $this->activity->log('media.deleted', $media);

        return back()->with('status', 'Media deleted.');
    }
}
