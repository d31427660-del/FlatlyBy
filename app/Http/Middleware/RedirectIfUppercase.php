<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfUppercase
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Only redirect GET and HEAD requests to avoid disrupting POST/PUT/DELETE forms or API calls
        if ($request->isMethod('GET') || $request->isMethod('HEAD')) {
            $uri = $request->getRequestUri();
            
            // Check for /index.php or /public in the URL
            if (str_contains($uri, '/index.php') || str_contains($uri, '/public')) {
                // Remove /index.php and /public from the path
                $cleanUri = preg_replace('#/(index\.php|public)+#i', '', $uri);
                if (empty($cleanUri) || $cleanUri === '//' || $cleanUri === '/') {
                    $cleanUri = '/';
                } elseif (str_starts_with($cleanUri, '?')) {
                    $cleanUri = '/' . $cleanUri;
                }
                
                if ($cleanUri !== $uri) {
                    // Use redirect()->away with absolute URL to prevent Laravel from prepending the base URL (e.g. /public)
                    return redirect()->away($request->getSchemeAndHttpHost() . $cleanUri, 301);
                }
            }

            $path = $request->getPathInfo();
            $modified = false;
            $cleanPath = $path;

            // Redirect trailing slash if path is not '/'
            if (strlen($cleanPath) > 1 && str_ends_with($cleanPath, '/')) {
                $cleanPath = rtrim($cleanPath, '/');
                $modified = true;
            }

            // Check if the path contains any uppercase ASCII letters
            if (preg_match('/[A-Z]/', $cleanPath)) {
                $cleanPath = strtolower($cleanPath);
                $modified = true;
            }

            if ($modified) {
                $queryString = $request->server('QUERY_STRING') ?: $request->getQueryString();
                $redirectUrl = $cleanPath . ($queryString ? '?' . $queryString : '');

                return redirect()->away($request->getSchemeAndHttpHost() . $redirectUrl, 301);
            }
        }

        return $next($request);
    }
}
