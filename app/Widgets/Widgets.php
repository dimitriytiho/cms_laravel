<?php


namespace App\Widgets;


abstract class Widgets
{
    // Общий класс для всех виджетов (использовать если есть общая логика у виджетов)

    protected $model = null;

    public function __construct()
    {

    }
}
