<?php

namespace App\Http\Middleware;

use Closure;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use InstagramScraper\Instagram;

class InstaMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse) $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        try {
            $insta = Instagram::withCredentials(new Client(), '', '', null);
            $insta->loginWithSessionId(env('INSTA_SESSION'));
            return $next($request);

        } catch (\Exception $e) {
            return dd('you not logged in');
        }
    }
}
