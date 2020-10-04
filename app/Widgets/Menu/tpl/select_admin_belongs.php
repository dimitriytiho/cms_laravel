<?php

use App\Models\Main;
use Illuminate\Support\Facades\Lang;


$params = $params ?? null;
$lang = lang();
$id = $id ?? null;
$tab = $tab ?? null;
$disabledIDs = Main::get('disabledIDs') ?? [];

if (!empty($item)):
    $title = $item->title ?? null;
    $title = $title && Lang::has("{$lang}::s.{$item->title}") ? __("{$lang}::s.{$item->title}") : $title;

    ?>
    <option data-title="<?= $title; ?>" data-title-lang="<?= Lang::has("{$lang}::t.{$title}") ? __("{$lang}::t.{$title}") : $title; ?>" value="<?= $id; ?>"<?php if (in_array($id, $disabledIDs)) echo ' disabled'; ?>><?= $tab ? "{$tab} " : null; ?><?= $title; ?></option>
    <?php

    if (isset($item->childs)):
        echo $this->getMenuHtml($item->childs, "{$tab}-");
    endif;
endif;
