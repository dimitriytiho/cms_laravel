<?php

$parent = isset($item->childs);

if (!empty($item)):
    $title = $item->title ?? null;
    $slug = $item->slug ?? null;

    ?>
    <li>
        <a href="/<?= $slug; ?>"><?= $title; ?></a>
        <?php if ($parent): ?>
            <ul>
                <?= $this->getMenuHtml($item->childs); ?>
            </ul>
        <?php endif; ?>
    </li>
<?php endif;
