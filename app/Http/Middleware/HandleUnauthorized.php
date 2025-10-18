<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class HandleUnauthorized
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if ($response->getStatusCode() === 403) {
            return redirect()->route('home')->with('error', 'You are not authorized to access this page.');
        }

        return $response;
    }
}
