<?php

$lang = lang();
$params = $params ?? null;
$parent = isset($item->childs);

if (!empty($item)):
    $title = $item->title ?? null;
    $langTitle = Lang::has("{$lang}::t.{$title}") ? __("{$lang}::t.{$title}") : $title;
    $slug = $item->slug ?? null;
    $target = !empty($item->target) ? " target=\"{$item->target}\"" : null;
    $activeColor = request()->path() === $slug || Str::contains(request()->path(), trim($slug, '/')) ? ' active_color' : null;

    ?>
    <li class="<?= $params['classLi'] ?>">
        <a href="<?= $slug; ?>" class="<?= $params['classLink'] . $activeColor ?>"<?= $target ?>><?= $langTitle; ?></a>
        <?php if ($parent): ?>
            <ul>
                <?= $this->getMenuHtml($item->childs); ?>
            </ul>
        <?php endif; ?>
    </li>
<?php endif;
