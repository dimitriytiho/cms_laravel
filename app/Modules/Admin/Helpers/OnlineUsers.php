<?php


namespace App\Modules\Admin\Helpers;


use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class OnlineUsers
{
    public static $file = 'storage/users/online.log';
    public static $formatData = 'Y_m_d_H_i';
    public static $delimiter = '==';
    public static $delimiterRow = '||';
    public static $userData = ['id', 'name', 'email'];


    public static function getUsers()
    {
        $users = [];
        $file = self::filePath();

        if (File::exists($file)) {

            // Получаем содержимое файла
            $content = File::get($file);

            $date = self::getDate();

            // Определим последнюю дату в файле
            $contentDate = self::getLastDate($content);

            // Условие, по которому получаем данные
            if (self::ifDate($date, $contentDate)) {

                // Получаем содержимое файла
                $content = File::get($file);
                $content = explode(self::$delimiterRow, $content);

                if (!empty($content[0])) {
                    foreach ($content as $user) {
                        $user = explode(self::$delimiter, $user);
                        $userData = unserialize($user[0]);

                        // Сохраняем по уникальным ip
                        if (isset($userData['ip'])) {
                            $users[$userData['ip']] = $userData;
                        }
                    }
                }
            }
        }
        return $users;
    }


    // Условие, по которому получаем данные
    public static function ifDate($nowDate, $lastDate)
    {
        // Если последняя дата не равно текущей, то будем обновлять файл
        return $nowDate === $lastDate;
    }


    // Текущая дата, в указаном формате
    public static function getDate()
    {
        return date(self::$formatData);
    }


    // Определим последнюю дату в файле
    public static function getLastDate($contentFile)
    {
        if ($contentFile) {
            $delimiterLen = strlen(self::$delimiterRow);
            return substr($contentFile, -strlen(self::getDate()) -$delimiterLen, -$delimiterLen);
        }
        return false;
    }


    // Путь к файлу
    public static function filePath()
    {
        return base_path(self::$file);
    }
}
