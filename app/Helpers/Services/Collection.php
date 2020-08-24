<?php

// Данный класс даёт возможность объект класса использовать как массив.


namespace App\Helpers\Services;

class Collection implements \ArrayAccess, \Iterator
{
    use TCollection;
}
