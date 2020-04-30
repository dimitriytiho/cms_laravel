<?php


namespace App\Helpers;

use App\Lib\SMSRU;

class Sms
{
    // Возвращает телефонный номер без лишних символов, с 7 в начале и кол-во 11 символо, в противном случае вернёт false. Принимает телефонный номер.
    public static function sendOneSMS($phoneNumber, $textMessage)
    {
        $tel = self::onlyPhoneNumber($phoneNumber);
        if ($tel) {
            $smsru = new SMSRU(config('add.smsru')); // Ваш уникальный программный ключ, который можно получить на главной странице

            $data = new \stdClass();
            $data->to = $tel;
            $data->text = $textMessage; // Текст сообщения
            $data->from = 'OmegaKontur'; // Если у вас уже одобрен буквенный отправитель, его можно указать здесь, в противном случае будет использоваться ваш отправитель по умолчанию
// $data->time = time() + 7*60*60; // Отложить отправку на 7 часов
// $data->translit = 1; // Перевести все русские символы в латиницу (позволяет сэкономить на длине СМС)
// $data->test = 1; // Позволяет выполнить запрос в тестовом режиме без реальной отправки сообщения
// $data->partner_id = '1'; // Можно указать ваш ID партнера, если вы интегрируете код в чужую систему
            $sms = $smsru->send_one($data); // Отправка сообщения и возврат данных в переменную

            /*if ($sms->status == "OK") { // Запрос выполнен успешно
                echo "Сообщение отправлено успешно. ";
                echo "ID сообщения: $sms->sms_id. ";
                echo "Ваш новый баланс: $sms->balance";
            } else {
                echo "Сообщение не отправлено. ";
                echo "Код ошибки: $sms->status_code. ";
                echo "Текст ошибки: $sms->status_text.";
            }*/
        }
    }


    /*
     * Отправить одно СМС.
     * $phoneNumber - в формате '79631223344'.
     * $textMessage - текст сообщения, максимально 70 знаков.
     */

    public static function onlyPhoneNumber($phoneNumber)
    {
        $one = substr($phoneNumber, 0, 1);
        if ($one == 8) {
            $phoneNumber = 7 . substr($phoneNumber, 1);
        }
        $tel = str_replace(['+', '(', ')', '-', '_', ' '], '', $phoneNumber);
        if (strlen($tel) === 11) {
            return (int)$tel;
        }
        return false;
    }
}
