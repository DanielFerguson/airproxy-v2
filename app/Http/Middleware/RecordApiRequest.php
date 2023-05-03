<?php

namespace App\Http\Middleware;

use App\Jobs\StoreRequestRecord;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RecordApiRequest
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $requestable_id = $request->route('table_id');
        $requestable_type = 'table';

        if ($requestable_id === null) {
            return $next($request);
        }

        StoreRequestRecord::dispatchAfterResponse(
            type: $requestable_type,
            id: $requestable_id,
            ip_address: $request->ip(),
            user_agent: $request->userAgent(),
            headers: $request->headers->all(),
        );

        return $next($request);
    }
}
