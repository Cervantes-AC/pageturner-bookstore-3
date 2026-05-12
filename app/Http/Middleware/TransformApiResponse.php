<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Str;

class TransformApiResponse
{
    public function handle($request, Closure $next)
    {
        $response = $next($request);

        if ($response->isSuccessful() && str_contains($request->header('Accept'), 'application/json')) {
            $data = json_decode($response->getContent(), true);
            if (is_array($data)) {
                $response->setContent(json_encode($this->convertToCamelCase($data)));
            }
        }

        return $response;
    }

    protected function convertToCamelCase($data)
    {
        if (is_array($data)) {
            $result = [];
            foreach ($data as $key => $value) {
                $camelKey = Str::camel($key);
                $result[$camelKey] = is_array($value) ? $this->convertToCamelCase($value) : $value;
            }
            return $result;
        }
        return $data;
    }
}
