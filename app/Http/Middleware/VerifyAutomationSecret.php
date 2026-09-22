<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerifyAutomationSecret
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $secret = config('services.n8n.webhook_secret');
        $provided = $request->header('X-Automation-Secret');

        if (! $secret || ! $provided || ! hash_equals($secret, $provided)) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized: Invalid or missing automation secret.',
            ], Response::HTTP_UNAUTHORIZED);
        }

        return $next($request);
    }
}
