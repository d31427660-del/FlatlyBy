<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\SiteVisit;

class TrackSiteVisits
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Only track GET requests and ignore static assets or admin pages
        if ($request->isMethod('GET') && !$request->ajax()) {
            $path = $request->path();

            if (!str_starts_with($path, 'admin') && 
                !str_starts_with($path, 'api') && 
                !str_starts_with($path, 'images') &&
                !str_starts_with($path, 'build') &&
                !str_starts_with($path, 'storage') &&
                !str_ends_with($path, '.png') &&
                !str_ends_with($path, '.jpg') &&
                !str_ends_with($path, '.ico') &&
                !str_ends_with($path, '.css') &&
                !str_ends_with($path, '.js')) {
                
                try {
                    if (\Illuminate\Support\Facades\Schema::hasTable('site_visits')) {
                        $userAgent = $request->header('User-Agent', '');
                        
                        // Filter out search bots, crawlers and automated tools
                        if (empty($userAgent) || preg_match('/(bot|crawl|spider|slurp|facebookexternalhit|bingpreview|yandex|googlebot|duckduckgo|baidu|ahrefs|semrush|bytespider|httpclient|curl|wget)/i', $userAgent)) {
                            return $response;
                        }

                        $deviceType = 'desktop';
                        if (preg_match('/(tablet|ipad|playbook|silk)|(android(?!.*mobi))/i', $userAgent)) {
                            $deviceType = 'tablet';
                        } elseif (preg_match('/(up\.browser|up\.link|mmp|symbian|smartphone|midp|wap|phone|android|iemobile|iphone|ipad|ipod)/i', $userAgent)) {
                            $deviceType = 'mobile';
                        }

                        $ip = $request->ip() ?? '127.0.0.1';
                        $sessionId = session()->getId();
                        $visitedDate = now()->toDateString();
                        $fullUrl = substr($request->fullUrl(), 0, 500);

                        SiteVisit::create([
                            'ip_address' => $ip,
                            'session_id' => $sessionId,
                            'device_type' => $deviceType,
                            'page_url' => $fullUrl,
                            'visited_date' => $visitedDate,
                        ]);
                    }
                } catch (\Exception $e) {
                    // Silently ignore tracking errors
                }
            }
        }

        return $response;
    }
}
