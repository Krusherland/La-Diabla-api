<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Helpers\JwtAuth;

class AdminMiddleware
{
    /**
     * Handle an incoming request - Check if user is admin.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $identity = $request->attributes->get('identity');
        
        if (!$identity || !isset($identity->role) || $identity->role !== 'admin') {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized. Admin access required.'
            ], 403);
        }
        
        return $next($request);
    }
}
