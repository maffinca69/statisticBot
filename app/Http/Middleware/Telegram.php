<?php


namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class Telegram
{
    /**
     * @param Request $request
     * @param Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (empty($request->all()) || !$request->has('update_id')) {
            return response('Invalid request', 403);
        }

        return $next($request);
    }
}
