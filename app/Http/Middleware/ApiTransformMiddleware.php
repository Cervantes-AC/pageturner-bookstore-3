<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ApiTransformMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

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
