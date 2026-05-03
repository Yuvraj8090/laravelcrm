<?php

namespace App\Http\Controllers\Admin;

use App\Cms\Content\Models\Content;
use App\Core\Support\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class DashboardController extends AdminController
{
    public function index(Request $request): View
    {
        $website = $this->currentWebsite($request);

        $stats = Cache::remember("dashboard:stats:{$website->id}", now()->addMinutes(10), function () {
            return [
                'pages' => Content::query()->where('type', 'page')->count(),
                'posts' => Content::query()->where('type', 'post')->count(),
                'drafts' => Content::query()->where('status', 'draft')->count(),
                'published' => Content::query()->where('status', 'published')->count(),
            ];
        });

        $activities = ActivityLog::query()
            ->where('website_id', $website->id)
            ->latest()
            ->limit(10)
            ->get();

        return view('admin.dashboard', compact('website', 'stats', 'activities'));
    }
}
