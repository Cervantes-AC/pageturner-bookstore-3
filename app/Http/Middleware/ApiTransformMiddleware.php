<?php

namespace App\Http\Middleware;

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
        }

        return $response;
    }

    /**
     * Attach X-RateLimit-Limit, X-RateLimit-Remaining, and Retry-After headers.
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

        $attempts   = \Illuminate\Support\Facades\RateLimiter::attempts($limiterName . '|' . $key);
        $remaining  = max(0, $limit - $attempts);

        $response->headers->set('X-RateLimit-Limit', $limit);
        $response->headers->set('X-RateLimit-Remaining', $remaining);

        if ($response->getStatusCode() === 429) {
            $retryAfter = \Illuminate\Support\Facades\RateLimiter::availableIn($limiterName . '|' . $key);
            $response->headers->set('Retry-After', $retryAfter);
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
}
