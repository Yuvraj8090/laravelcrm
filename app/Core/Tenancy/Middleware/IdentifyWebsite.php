<?php

namespace App\Core\Tenancy\Middleware;

use App\Core\Tenancy\Context\WebsiteContext;
use App\Core\Website\Models\Website;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class IdentifyWebsite
{
    public function handle(Request $request, Closure $next)
    {
        $host = $request->getHost();

        $website = Website::query()
            ->where('primary_domain', $host)
            ->orWhereHas('domains', fn ($query) => $query->where('domain', $host))
            ->first();

        if (!$website) {
            throw new NotFoundHttpException('No website is configured for this domain.');
        }

        app(WebsiteContext::class)->set($website);

        return $next($request);
    }
}
