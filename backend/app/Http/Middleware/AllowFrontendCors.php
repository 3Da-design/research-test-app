<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AllowFrontendCors
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $request->isMethod('OPTIONS')
            ? response('', 204)
            : $next($request);

        $allowOrigin = $this->resolveAllowOrigin($request);
        $response->headers->set('Access-Control-Allow-Origin', $allowOrigin);
        $response->headers->set('Access-Control-Allow-Methods', 'GET, POST, PUT, PATCH, DELETE, OPTIONS');
        $response->headers->set('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With');

        return $response;
    }

    private function resolveAllowOrigin(Request $request): string
    {
        $origin = $request->headers->get('Origin');
        if ($origin !== null && $this->isAllowedLocalDevOrigin($origin)) {
            return $origin;
        }

        return 'http://localhost:5173';
    }

    private function isAllowedLocalDevOrigin(string $origin): bool
    {
        return (bool) preg_match(
            '#^https?://(localhost|127\.0\.0\.1|\[::1\])(:\d+)?$#',
            $origin
        );
    }
}
