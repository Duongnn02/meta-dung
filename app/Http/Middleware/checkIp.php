<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Symfony\Component\HttpFoundation\Response;

class checkIp
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $ip = $request->ip();
        $response = Http::get("https://ipinfo.io/{$ip}/json");

        if ($response->successful()) {
            $data = $response->json();
            $country = $data['country'] ?? "";
            if($country == "VN") abort(403);
        }
        return $next($request);
    }
}
