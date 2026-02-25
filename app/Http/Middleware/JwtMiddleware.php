<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Helpers\JwtAuth;

class JwtMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $jwtAuth = new JwtAuth();
        $token = $request->header('Authorization');
        
        if (!$token) {
            return response()->json([
                'status' => 'error',
                'message' => 'Authorization token not found'
            ], 401);
        }
        
        // Remove "Bearer " prefix if present
        $token = str_replace('Bearer ', '', $token);
        $checkToken = $jwtAuth->checkToken($token);
        
        if ($checkToken) {
            $identity = $jwtAuth->checkToken($token, true);
            $request->attributes->set('identity', $identity);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid or expired token'
            ], 401);
        }
        
        return $next($request);
    }
}
