<?php

use \Illuminate\Support\Facades\Route;
use \App\App;


$lang = lang();
$route = App::get('c');
$id = (int)App::get('id');
$editLink = $id && Route::has("admin.$route.edit") ? route("admin.$route.edit", $id) : null;

?>
<div class="panel-dashboard">
    <a href="<?= route('admin.main'); ?>" class="panel-dashboard__icons" title="<?= __("{$lang}::a.Dashboard"); ?>">
        <svg class="panel-dashboard__tachometer">
            <use xlink:href="<?= asset('svg/dashboard_sprite.svg#tachometer-alt'); ?>"></use>
        </svg>
    </a>
    <?php


    if ($editLink):

        ?>
        <a href="<?= $editLink; ?>" class="panel-dashboard__icons" target="_blank" title="<?= __("{$lang}::a.edit"); ?>">
            <svg class="panel-dashboard__edit">
                <use xlink:href="<?= asset('svg/dashboard_sprite.svg#edit'); ?>"></use>
            </svg>
        </a>
    <?php endif; ?>
</div>
