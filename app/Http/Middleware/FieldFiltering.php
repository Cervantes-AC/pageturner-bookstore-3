<?php

namespace App\Http\Middleware;

use Closure;

class FieldFiltering
{
    public function handle($request, Closure $next)
    {
        $response = $next($request);

        if ($request->filled('fields') && $response->isSuccessful() && str_contains($request->header('Accept'), 'application/json')) {
            $fields = explode(',', $request->fields);
            $data = json_decode($response->getContent(), true);

            if (is_array($data)) {
                if (isset($data['data'])) {
                    $data['data'] = $this->filterFields($data['data'], $fields);
                } else {
                    $data = $this->filterFields($data, $fields);
                }
                $response->setContent(json_encode($data));
            }
        }

        return $response;
    }

    protected function filterFields($items, $fields)
    {
        if (is_array($items) && isset($items[0])) {
            return array_map(function ($item) use ($fields) {
                return array_intersect_key($item, array_flip($fields));
            }, $items);
        }

        if (is_array($items)) {
            return array_intersect_key($items, array_flip($fields));
        }

        return $items;
    }
}
