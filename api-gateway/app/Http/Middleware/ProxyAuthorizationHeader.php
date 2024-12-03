<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ProxyAuthorizationHeader
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        $authHeader = $request->header('Authorization');
        // info(['Authorization Header in Middleware' => $authHeader]);
        if ($authHeader) {
            $request->headers->set('Authorization', $authHeader);
            $request->server->set('HTTP_AUTHORIZATION', $authHeader);
        } else {
            return response()->json(['error' => 'Authorization header missing'], 401);
        }

        return $next($request);
    }
}
