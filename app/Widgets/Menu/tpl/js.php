<?php

$parent = isset($item->childs);

if (!empty($item)) {
    $part = "{$item->id}: {";
    foreach ($item as $k => $v) {
        $part .= "{$k}: '{$v}',";

        if ($k === 'childs') {
            $part .= "{$k}: {" . self::getMenuHtml($item->childs) . '},';
        }
    }
    $part = rtrim($part, ',');
    $part .= '},';
    echo $part;
}
