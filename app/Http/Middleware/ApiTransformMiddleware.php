<?php

namespace App\Http\Middleware;

use App\Models\ApiRateLimit;
use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Symfony\Component\HttpFoundation\Response;

class ApiTransformMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Add X-RateLimit headers to every response
        $this->addRateLimitHeaders($request, $response);

        if ($response instanceof JsonResponse) {
            $data = $response->getData(true);

            // Convert snake_case keys to camelCase
            $data = $this->convertKeysToCamelCase($data);

            // Field filtering: ?fields=id,title,price
            if ($request->filled('fields')) {
                $fields = explode(',', $request->query('fields'));
                $data   = $this->filterFields($data, $fields);
            }

            $response->setData($data);

            // ETag support for conditional requests (Section 4.4.2)
            $this->applyEtag($request, $response);
        }

        return $response;
    }

    /**
     * Attach X-RateLimit-Limit, X-RateLimit-Remaining, and Retry-After headers.
     * Also log rate limit hits to the api_rate_limits table.
     */
    private function addRateLimitHeaders(Request $request, Response $response): void
    {
        $limiterName = 'api';
        if ($request->routeIs('login', 'register', 'password.*')) {
            $limiterName = 'auth';
        } elseif (!$request->user()) {
            $limiterName = 'public';
        }

        $user = $request->user();
        $limit = match (true) {
            $user?->isAdmin()          => 1000,
            $user?->role === 'premium' => 300,
            $user !== null             => 60,
            $limiterName === 'auth'    => 10,
            default                    => 30,
        };

        $key = $user
            ? ($user->isAdmin() ? 'admin|' . $user->id : 'standard|' . $user->id)
            : $request->ip();

        $attempts   = RateLimiter::attempts($limiterName . '|' . $key);
        $remaining  = max(0, $limit - $attempts);

        $response->headers->set('X-RateLimit-Limit', $limit);
        $response->headers->set('X-RateLimit-Remaining', $remaining);

        $throttled = $response->getStatusCode() === 429;

        if ($throttled) {
            $retryAfter = RateLimiter::availableIn($limiterName . '|' . $key);
            $response->headers->set('Retry-After', $retryAfter);
        }

        // Log to api_rate_limits table (sampled to avoid overwhelming the DB)
        if (mt_rand(1, 10) === 1 || $throttled) {
            try {
                ApiRateLimit::create([
                    'user_id'    => $user?->id,
                    'ip_address' => $request->ip(),
                    'tier'       => $limiterName === 'api' ? ($user ? ($user->isAdmin() ? 'admin' : ($user->role === 'premium' ? 'premium' : 'standard')) : 'public') : $limiterName,
                    'endpoint'   => $request->path(),
                    'method'     => $request->method(),
                    'attempts'   => $attempts,
                    'limit'      => $limit,
                    'throttled'  => $throttled,
                    'user_agent' => $request->userAgent(),
                ]);
            } catch (\Exception $e) {
                // Don't let logging failure break the request
            }
        }
    }

    private function convertKeysToCamelCase(array $data): array
    {
        $result = [];
        foreach ($data as $key => $value) {
            $camel = lcfirst(str_replace('_', '', ucwords($key, '_')));
            $result[$camel] = is_array($value) ? $this->convertKeysToCamelCase($value) : $value;
        }
        return $result;
    }

    private function filterFields(array $data, array $fields): array
    {
        if (isset($data[0]) && is_array($data[0])) {
            return array_map(fn($item) => array_intersect_key($item, array_flip($fields)), $data);
        }
        return array_intersect_key($data, array_flip($fields));
    }

    /**
     * Apply ETag support for conditional requests.
     * If the response already has an ETag (set by the controller), use it;
     * otherwise generate one from the response content.
     * Respond with 304 Not Modified if the ETag matches.
     */
    private function applyEtag(Request $request, JsonResponse $response): void
    {
        if (!$response->headers->has('ETag')) {
            $content = $response->getContent();
            if ($content) {
                $response->setEtag(md5($content));
            }
        }

        $etag = $response->headers->get('ETag');
        if (!$etag) return;

        $requestEtag = $request->header('If-None-Match');

        // Handle weak and strong comparison
        $requestEtag = trim((string) $requestEtag, '"');

        if ($requestEtag === trim($etag, '"') || $requestEtag === 'W/"' . trim($etag, '"') . '"') {
            $response->setStatusCode(304);
            $response->setContent(null);
        }
    }
}
