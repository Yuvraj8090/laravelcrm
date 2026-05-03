<?php

namespace App\Http\Controllers\Admin;

use App\Core\Tenancy\Context\WebsiteContext;
use App\Core\Website\Models\Website;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

abstract class AdminController extends Controller
{
    protected function currentWebsite(Request $request): Website
    {
        $websiteId = $request->integer('website_id', (int) $request->session()->get('admin_website_id', 0));

        $website = $websiteId
            ? Website::query()->find($websiteId)
            : Website::query()->orderBy('name')->firstOrFail();

        $request->session()->put('admin_website_id', $website->id);
        app(WebsiteContext::class)->set($website);

        view()->share('currentWebsite', $website);
        view()->share('adminWebsites', Website::query()->orderBy('name')->get());

        return $website;
    }
}
