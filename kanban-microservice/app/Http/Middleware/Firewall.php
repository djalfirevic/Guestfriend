<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Laravel\Lumen\Http\ResponseFactory;

/**
 * Class Firewall
 *
 * @package App\Http\Middleware
 */
class Firewall
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @return Response|ResponseFactory|mixed
     */
    public function handle($request, Closure $next)
    {
        $whitelistedIps = config('app.whitelisted_ips');
        $ips = !empty($whitelistedIps) ? explode('|', $whitelistedIps) : [];
        $requestIp = $request->ip();

        if (!empty($ips) && !in_array($requestIp, $ips)) {
            return response('Forbidden.', JsonResponse::HTTP_FORBIDDEN);
        }

        return $next($request);
    }
}
