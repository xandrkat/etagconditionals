<?php

namespace Werk365\EtagConditionals\Middleware;

use Closure;
use Illuminate\Http\Request;

class IfNoneMatch extends Middleware
{
    public string $middleware = 'ifNoneMatch';

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Handle request
        $method = $request->getMethod();

        // Support using HEAD method for checking If-None-Match
        if ($request->isMethod('HEAD')) {
            $request->setMethod('GET');
        }

        //Handle response
        $response = $next($request);

        $etag = '"'.md5($response->getContent()).'"';
        $noneMatch = $request->getETags();

        if (in_array($etag, $noneMatch)) {
            $response->setNotModified();
        }

        $request->setMethod($method);

        return $response;
    }
}
