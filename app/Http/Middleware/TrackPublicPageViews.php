<?php

namespace App\Http\Middleware;

use App\Models\PageViewEvent;
use App\Models\Plot;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class TrackPublicPageViews
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if (! $this->shouldTrack($request, $response)) {
            return $response;
        }

        try {
            $sessionId = $this->resolveSessionId($request, $response);
            $routeName = (string) $request->route()?->getName();
            $plotId = $this->resolvePlotId($request);

            PageViewEvent::query()->create([
                'visited_at' => now(),
                'route_name' => $routeName,
                'page_key' => $routeName,
                'path' => '/'.$request->path(),
                'plot_id' => $plotId,
                'session_id' => $sessionId,
                'visitor_hash' => $this->buildVisitorHash($request),
                'metadata' => [
                    'query' => $request->query(),
                ],
            ]);
        } catch (Throwable $exception) {
            report($exception);
        }

        return $response;
    }

    protected function shouldTrack(Request $request, Response $response): bool
    {
        if (! config('analytics.page_views.enabled', true)) {
            return false;
        }

        if (! $request->isMethod('GET')) {
            return false;
        }

        if (! $response->isSuccessful()) {
            return false;
        }

        if ($this->isInExcludedEnvironment()) {
            return false;
        }

        if ($this->isBot($request)) {
            return false;
        }

        $routeName = $request->route()?->getName();
        $trackedRoutes = array_keys(config('analytics.page_views.tracked_routes', []));

        if (! is_string($routeName) || ! in_array($routeName, $trackedRoutes, true)) {
            return false;
        }

        $contentType = (string) $response->headers->get('Content-Type', '');

        return str_contains($contentType, 'text/html');
    }

    protected function isInExcludedEnvironment(): bool
    {
        $excludedEnvironments = config('analytics.page_views.excluded_environments', []);

        foreach ($excludedEnvironments as $excludedEnvironment) {
            if (is_string($excludedEnvironment) && app()->environment($excludedEnvironment)) {
                return true;
            }
        }

        return false;
    }

    protected function isBot(Request $request): bool
    {
        $userAgent = strtolower((string) $request->userAgent());

        if ($userAgent === '') {
            return false;
        }

        $patterns = config('analytics.page_views.bot_user_agent_patterns', []);
        foreach ($patterns as $pattern) {
            if (! is_string($pattern)) {
                continue;
            }

            if (str_contains($userAgent, strtolower($pattern))) {
                return true;
            }
        }

        return false;
    }

    protected function resolveSessionId(Request $request, Response $response): string
    {
        $cookieName = (string) config('analytics.page_views.cookie_name', 'pv_sid');
        $sessionId = $request->cookie($cookieName);

        if (is_string($sessionId) && $sessionId !== '') {
            return $sessionId;
        }

        $sessionId = (string) Str::uuid();
        $minutes = (int) config('analytics.page_views.cookie_minutes', 60 * 24 * 365);

        $response->headers->setCookie(new Cookie(
            $cookieName,
            $sessionId,
            now()->addMinutes($minutes),
            '/',
            null,
            false,
            true,
            false,
            Cookie::SAMESITE_LAX
        ));

        return $sessionId;
    }

    protected function buildVisitorHash(Request $request): string
    {
        $fingerprint = implode('|', [
            (string) $request->ip(),
            (string) $request->userAgent(),
        ]);

        return hash_hmac('sha256', $fingerprint, (string) config('app.key'));
    }

    protected function resolvePlotId(Request $request): ?int
    {
        $plot = $request->route()?->parameter('plot');

        if ($plot instanceof Plot) {
            return $plot->id;
        }

        if (is_numeric($plot)) {
            return (int) $plot;
        }

        return null;
    }
}
