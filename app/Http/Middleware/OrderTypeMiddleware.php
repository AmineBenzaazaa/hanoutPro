<?php

namespace App\Http\Middleware;

use Closure;

class OrderTypeMiddleware
{
    public function handle($request, Closure $next)
    {
        // Get the authenticated user, assuming you're using some form of authentication.
        $user = auth()->user();

        // Check the user's role to determine the order type
        if ($user->role === 'supplier' || $user->role === 'store') {
            $request->merge(['order_type' => 'supplier_store']);
        } elseif ($user->role === 'client') {
            $request->merge(['order_type' => 'store_client']);
        }

        return $next($request);
    }
}
