<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BannedIp
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     *
     *
     * Если текущий IP находится в таблице banned_ip и banned = 1, то он блокируется
     */
    public function handle($request, Closure $next)
    {
        $ip = $request->ip() ?? null;
        $ipData = DB::table('banned_ip')->where('ip', $ip)->get();
        $ipBanned = $ipData[0]->banned ?? null;
        if ($ipBanned != 1) {
            return $next($request);
        }
        die();
    }
}
