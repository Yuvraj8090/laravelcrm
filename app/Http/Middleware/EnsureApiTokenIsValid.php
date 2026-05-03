<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureApiTokenIsValid
{
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->bearerToken();

        abort_unless($token, 401, 'API token missing.');

        $user = User::query()->where('api_token', hash('sha256', $token))->first();

        abort_unless($user, 401, 'API token is invalid.');

        auth()->setUser($user);
        $request->setUserResolver(fn () => $user);

        return $next($request);
    }
}
