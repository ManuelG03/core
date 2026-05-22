<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class Cors
{
    public function handle(Request $request, Closure $next)
    {
        $headers = [
            'Access-Control-Allow-Origin' => 'http://localhost:5173',
            'Access-Control-Allow-Methods' => 'GET, POST, PUT, DELETE, OPTIONS',
            'Access-Control-Allow-Headers' => 'Content-Type, Authorization, X-CSRF-TOKEN',
            'Access-Control-Allow-Credentials' => 'true'
        ];

        if ($request->getMethod() === 'OPTIONS') {
            return response('', 200, $headers);
        }

        $response = $next($request);

        foreach ($headers as $key => $value) {
            if (is_object($response) && property_exists($response, 'headers') && is_object($response->headers)) {
                $response->headers->set($key, $value);
            } elseif (is_object($response) && method_exists($response, 'header')) {
                $response->header($key, $value);
            }
        }

        return $response;
    }
}

