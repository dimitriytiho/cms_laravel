<?php

use App\Main;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Request;


$lang = lang();
$id = $id ?? null;
$tab = $tab ?? null;
$parentID = Main::get('parent_id') ?? '0';
$getID = Request::segment(3);

if (!empty($item)):
    $title = $item->title ?? null;
    $title = $title && Lang::has("{$lang}::c.{$item->title}") ? __("{$lang}::c.{$item->title}") : $title;

    ?>
    <option value="<?= $id; ?>"<?php if ($id == $parentID) echo ' selected'; if ($getID == $id) echo ' disabled'; ?>><?= $tab ? "{$tab} " : null; ?><?= $title; ?></option>
    <?php

    if (isset($item->childs)) {
        echo $this->getMenuHtml($item->childs, "{$tab}-");
    }
endif;
