<?php


namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;


class CheckBotToken
{
    /**
     * @param Request $request
     * @param Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $currentToken = config('bot.api_key');
        $requestToken = $request->route('token', false);

        if ($requestToken === $currentToken) {
            return $next($request);
        }

        return response('Invalid token', 403);
    }
}
