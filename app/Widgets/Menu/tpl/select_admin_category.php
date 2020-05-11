<?php

use App\Main;
use Illuminate\Support\Facades\Lang;


$lang = lang();
$id = $id ?? null;
$tab = $tab ?? null;
$disabledIDs = Main::get('disabledIDs') ?? [];

if (!empty($item)):
    $title = $item->title ?? null;
    $title = $title && Lang::has($title) ? __("{$lang}::c.{$item->title}") : $title;

    ?>
    <option data-title="<?= $title; ?>" value="<?= $id; ?>"<?php if (in_array($id, $disabledIDs)) echo ' disabled'; ?>><?= $tab ? "{$tab} " : null; ?><?= $title; ?></option>
    <?php

    if (isset($item->childs)):
        echo $this->getMenuHtml($item->childs, "{$tab}-");
    endif;
endif;
