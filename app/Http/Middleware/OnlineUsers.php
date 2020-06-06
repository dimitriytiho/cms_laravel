<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\File;
use App\Modules\Admin\Helpers\OnlineUsers as helpersOnlineUsers;

class OnlineUsers
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $user = [
            'ip' => $request->ip() ?: 'no ip',
        ];
        $delimiter = helpersOnlineUsers::$delimiter;
        $delimiterRow = helpersOnlineUsers::$delimiterRow;
        $date = helpersOnlineUsers::getDate();

        // Если пользователь авторизирован, то заполняем его данные
        if (auth()->check()) {

            $userData = helpersOnlineUsers::$userData;
            if ($userData) {
                foreach ($userData as $userKey) {
                    $user += [
                        $userKey => auth()->user()->$userKey,
                    ];
                }
            }

        }
        $row = serialize($user) . $delimiter . $date . $delimiterRow;
        $file = helpersOnlineUsers::filePath();

        if (File::exists($file)) {

            // Получаем содержимое файла
            $content = File::get($file);

            // Определим последнюю дату в файле
            $contentDate = helpersOnlineUsers::getLastDate($content);

            // Условие, по которому получаем данные
            if (helpersOnlineUsers::ifDate($date, $contentDate)) {

                // Допишем в конец файла
                File::append($file, $row);

            } else {

                // Перезапишем файл
                File::replace($file, $row);
            }
        }

        return $next($request);
    }
}
