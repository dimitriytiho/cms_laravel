<?php

$lang = lang();
$groups = self::$groups ?? null;
$values = self::$values ?? null;


if ($groups): ?>
    <div class="filter">
        <?php foreach ($groups as $key => $group): ?>
            <?php

            // Если чекбокс
            if ($group->type === 'checkbox'): ?>
                <div class="row">
                    <div class="col-12">
                        <h3 class="<?= $key ? 'mt-4' : null ?>"><?= Lang::has("{$lang}::t.{$group->title}") ? __("{$lang}::t.{$group->title}") : $group->title ?></h3>
                    </div>
                    <div class="col-12">
                        <?php if ($values): ?>
                            <?php foreach ($values[$group->id] as $id => $filter): ?>
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" id="<?= "{$filter}_{$id}" ?>">
                                    <label class="custom-control-label" for="<?= "{$filter}_{$id}" ?>"><?= Lang::has("{$lang}::t.{$filter}") ? __("{$lang}::t.{$filter}") : $filter ?></label>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
                <?php

                // Если радиокнопка
                /*elseif ($group->type === 'radio'):*/ ?>
            <?php endif; ?>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
