<?php

namespace App\Http\Controllers\Api;

use App\Cms\Content\Models\Content;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ContentController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $contents = Content::query()
            ->with('taxonomies', 'sections')
            ->when($request->filled('type'), fn ($query) => $query->where('type', $request->string('type')->toString()))
            ->latest('published_at')
            ->paginate(20);

        return response()->json($contents);
    }

    public function show(Content $content): JsonResponse
    {
        return response()->json($content->load('taxonomies', 'sections'));
    }
}
