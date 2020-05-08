<?php

namespace App\Http\Middleware;

use App\App;
use Closure;

class AccessIpAdmin
{
    /**
     *
     * Если Ip из настроек access_ip, совпадает с текущем, то разрешим доступ.
     * Если не нужен доступ по Ip удалить эту настройку или сохраните 0.
     *
     *
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        // Разрешённый Ip для локальной машины 127.0.0.1
        $allowedIps = App::get('settings')['access_ip'] ?? null;

        // Если не 0, то проверем Ip
        if ($allowedIps) {
            $ip = $request->ip();
            $allowedIps = str_replace(' ', '', $allowedIps);
            $allowedIps = explode(',', $allowedIps);

            foreach ($allowedIps as $allowedIp) {
                $partsAllowed = explode('.', trim($allowedIp));
                $partsCurr = explode('.', $ip);

                foreach ($partsAllowed as $k => &$item) {
                    if ($item == '*') {
                        $item = $partsCurr[$k];
                    }
                }

                unset($item);
                $allowedIp = implode('.', $partsAllowed);

                if ($allowedIp === $ip) {

                    // Продолжим
                    return $next($request);
                }
            }

            // Запишем в логи и показажем страницу 404
            App::getError('Request no Access Ip', __METHOD__, false);
            return redirect()->route('not_found');
        }

        return $next($request);
    }
}
