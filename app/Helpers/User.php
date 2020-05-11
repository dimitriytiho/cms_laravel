<?php


namespace App\Helpers;


use App\Main;
use App\Modules\Admin\Models\BannedIp;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class User
{
    /*
     * После n раз (10 по-умолчанию), когда сработает laravel защита попыток входа, пользователь будет заблокирован к авторизации.
     * На стандартной авторизации этот функционал не используется, только специальной странице входа в админку.
     * Если этот функционал не нужен в админке в настройках banned_ip_count напишите 0.
     */
    public static function bannedUser()
    {
        // Через n кол-во блокировой блокируется IP c помощью таблицы banned_ip
        $tableBanned = 'banned_ip';
        $bannedIpCount = (int)(Main::site('banned_ip_count'));
        $ip = request()->ip() ?? null;

        if ($bannedIpCount && $ip) {
            $issetIp = $values = DB::table($tableBanned)->where('ip', $ip)->get();

            // Если существует IP в таблице banned_ip
            if (isset($issetIp[0])) {
                $countLast = (int)$issetIp[0]->count;
                $ifCountBig = $countLast > $bannedIpCount;

                // Если число попыток больше n в настройках banned_ip_count
                if ($ifCountBig) {

                    // Обновить запись с этим ip, изменив на banned = 1
                    DB::table($tableBanned)->where('ip', $ip)->update(['banned' => '1']);

                } else {

                    // Обновить запись с этим ip, прибавив 1 к полю count
                    DB::table($tableBanned)->where('ip', $ip)->increment('count', 1);
                }

            // Если нет IP в таблице banned_ip
            } else {

                $data['ip'] = $ip;
                $bannedIP = new BannedIp();
                $bannedIP->fill($data);

                if (!$bannedIP->save()) {

                    // Сообщение что-то пошло не так
                    $message = 'Error Banned IP save and in ' . __METHOD__;
                    Log::warning($message);
                }
            }
        }
    }
}
