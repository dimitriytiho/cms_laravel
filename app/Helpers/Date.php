<?php


namespace App\Helpers;


class Date
{
    // Возвращает массив месяцев
    public static function months() {
        return [
            1 => 'january',
            2 => 'february',
            3 => 'march',
            4 => 'april',
            5 => 'may',
            6 => 'june',
            7 => 'july',
            8 => 'august',
            9 => 'september',
            10 => 'october',
            11 => 'november',
            12 => 'december',
        ];
    }


    // Возвращает время в метке Unix: 1544636288, принимает дату: 2017-09-01 00:00:00
    public static function timestampToTime($date)
    {
        if ($date && $date != '0000-00-00 00:00:00') {
            return strtotime($date);
        }
        return false;
    }


    // Возвращает время в формате: 2017-09-01 00:00:00, принимает дату в метке Unix: 1544636288
    public static function timeToTimestamp($date)
    {
        if ($date) {
            return date('Y-m-d H:i:s', (int)$date);
        }
        return false;
    }


    /*
     * Возвращает время даты на конец месяца (формат метки Unix: 1544636288).
     * $month -  передать null, будет дата на конец недели.
     */
    public static function timeEndDay($month = true)
    {
        if ($month) {
            // Остаток времени до конца сегодняшнего дня
            $end_day = strtotime('tomorrow') - time();

            // Количество полных дней до конца месяца в time
            $end_month = (date('t') - date('j')) * 86400;

            return time() + $end_day + $end_month;

        } else {

            // Остаток времени до конца сегодняшнего дня
            $end_day = strtotime('tomorrow') - time();

            // Количество полных дней до конца недели в time
            $end_week = (7 - date('N')) * 86400;

            return time() + $end_day + $end_week;
        }
    }


    /*
     * Возвращает в массиве года, от минимального до текущего.
     * $min_year - от какого года начинать.
     * $max_year - необязательный параметр, каким годом заканчивать, по-умолчанию текущий.
     */
    public static function loopDate($min_year, $max_year = null)
    {
        $min_year = substr((int)$min_year, 0, 4);
        $max_year = $max_year ?: date('Y');
        $max_year = substr((int)$max_year, 0, 4);
        if ($min_year > $max_year) {
            return false;
        }

        while($min_year <= $max_year) {
            $year[] = $min_year;
            $min_year++;
        }
        return array_reverse($year);
    }
}
