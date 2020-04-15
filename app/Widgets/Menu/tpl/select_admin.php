<?php

$id = $id ?? null;
$tab = $tab ?? null;
$parentID = \App\App::$registry->get('parent_id') ?? '0';
$getID = \Illuminate\Support\Facades\Request::segment(3);

if (!empty($item)):
    $title = $item->title ?? null;
    $title = $title && \Illuminate\Support\Facades\Lang::has("c.{$item->title}") ? __("c.{$item->title}") : $title;

    ?>
    <option value="<?= $id; ?>"<?php if ($id == $parentID) echo ' selected'; if ($getID == $id) echo ' disabled'; ?>><?= $tab ? "{$tab} " : null; ?><?= $title; ?></option>
    <?php

    if (isset($item->childs)):
        echo $this->getMenuHtml($item->childs, "{$tab}-");
    endif;
endif;
