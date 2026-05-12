<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Cache\RateLimiter;
use Symfony\Component\HttpFoundation\Response;

class ApiRateLimiter
{
    protected $limiter;

    public function __construct(RateLimiter $limiter)
    {
        $this->limiter = $limiter;
    }

    public function handle($request, Closure $next, $tier = 'public')
    {
        $limits = config("rate-limiting.tiers.{$tier}", ['limit' => 30, 'decay' => 60]);

        $key = $request->user()
            ? "api|user:{$request->user()->id}"
            : "api|ip:{$request->ip()}";

        if ($this->limiter->tooManyAttempts($key, $limits['limit'])) {
            $retryAfter = $this->limiter->availableIn($key);

            return response()->json([
                'message' => 'Too many requests.',
                'retry_after' => $retryAfter,
                'limit' => $limits['limit'],
            ], 429)->withHeaders([
                'X-RateLimit-Limit' => $limits['limit'],
                'X-RateLimit-Remaining' => 0,
                'Retry-After' => $retryAfter,
            ]);
        }

        $this->limiter->hit($key, $limits['decay']);

        $remaining = max(0, $limits['limit'] - $this->limiter->attempts($key));

        $response = $next($request);

        $response->headers->set('X-RateLimit-Limit', $limits['limit']);
        $response->headers->set('X-RateLimit-Remaining', $remaining);

        return $response;
    }
}
