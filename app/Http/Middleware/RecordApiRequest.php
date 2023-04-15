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
        $is_data_route = str_contains($request->path(), '/data/');

        $requestable_id = $is_data_route
            ? $request->route('table_id')
            : $request->route('asset_id');

        $requestable_type = $is_data_route
            ? 'table'
            : 'asset';

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
