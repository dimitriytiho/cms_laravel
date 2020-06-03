<?php

use App\Helpers\Str;


/*
 * $idIcon - id иконки из спрайта.
 * $width - ширина иконки, необязательный параметр.
 * $height - высота иконки, необязательный параметр.
 * $class - класс, необязательный параметр.
 * $style - в тег style написать стили, необязательный параметр.
 * $attrs - дополнительные атрибуты, необязательный параметр.
 */
function icon($idIcon, $width = null, $height = null, $class = null, $style = null, $attrs = null)
{
    if ($idIcon) {
        $width = $width ? "width=\"{$width}\"" : null;
        $height = $height ? "height=\"{$height}\"" : null;
        $class = $class ? "class=\"{$class}\"" : null;
        $style = $style ? "style=\"{$style}\"" : null;
        $svg = IMG . '/svg/icon.svg';
        $path = asset("{$svg}#{$idIcon}");

        if (is_file(public_path($svg))) {
            return <<<S
<svg $width $height $class $attrs $style aria-hidden="true">
    <use xlink:href="{$path}"></use>
</svg>
S;
        }
    }
    return false;
}


/*
 * Возвращает картинку svg вместе с заменяющей её картинкой.
 * $img - путь с названием картинки.
 * $alt - текст для картинки.
 * $width - ширина для картинки, не забудьте написать единицы измерения, необязательный параметр.
 * $img_svg - путь с названием картинки, svg файл, если не передавать, то возьмётся $img, необязательный параметр.
 * $class - класс для тега img, необязательный параметр.
 * $id - id для тега img, необязательный параметр.
 * $classPicture - класс для тега picture, необязательный параметр.
 */
function svg($img, $alt = null, $width = null, $img_svg = null, $class = null, $id = null, $classPicture = null) {
    $width = $width ? "width='{$width}'" : null;
    $path = pathinfo($img);
    $path_dir = $path['dirname'] === '.' ? null : "{$path['dirname']}/";
    $img_svg = $img_svg ?: "$path_dir{$path['filename']}.svg";
    $id = $id ? "id='{$id}'" : null;
    $alt = $alt ? Str::removeTag($alt) : ' ';
    $classPicture = $classPicture ? "class='{$classPicture}'" : null;

    if (is_file(config('add.imgPath') . "/{$img}") && is_file(config('add.imgPath') . "/{$img_svg}")) {
        $img = asset(config('add.img') . "/{$img}");
        $img_svg = asset(config('add.img') . "/{$img_svg}");

        return <<<S
<picture {$classPicture}>
    <source srcset="{$img}" type="image/svg+xml">
    <img src="{$img_svg}" class="responsive-img {$class}" $id alt="{$alt}" {$width}>
</picture>
S;
    }
    return false;
}


/*
 * Возвращает input для формы.
 * $name - передать название, перевод будет взять из /app/Modules/lang/en/f.php.
 * $idForm - если используется форма несколько раз на странице, то передайте id формы, чтобы у id у чекбоксова были оригинальные id.
 * $required - если input необязательный, то передайте null, необязательный параметр.
 * $type - тип input, по-умолчанию text, необязательный параметр.
 * $value - передать значение, необязательный параметр.
 * $label - если он нужен, то передать true, необязательный параметр.
 * $placeholder - если нужен другой текст, то передать его, необязательный параметр.
 * $class - передайте свой класс, необязательный параметр.
 * $attrs - передайте необходимые параметры строкой или в массиве ['id' => 'test', 'data-id' => 'dataTest', 'novalidate' => ''], необязательный параметр.
 */
function input($name, $idForm = false, $required = true, $type = null, $value = null, $label = false, $placeholder = false, $class = false, $attrs = false)
{
    $lang = lang();
    $title = Lang::has("{$lang}::f.{$name}") ? __("{$lang}::f.{$name}") : $name;
    $id = $idForm ? "{$idForm}_{$name}" : $name;

    $required = $required ? 'required' : null;
    $type = $type ? $type : 'text';
    $star = $required ? '<sup>*</sup>' : null;
    $value = $value ?? old($name) ?? null;

    $placeholderStar = $label && $required ? '*' : null;
    $placeholderLabel = !$label && $required ? '...' : null;
    $placeholder = $placeholder ?: $title . $placeholderStar . $placeholderLabel;
    $label = $label ? null : 'class="sr-only"';

    $_required = __("{$lang}::f.required");
    $_required = $required ? "<div class=\"invalid-feedback\">{$_required}</div>" : null;
    $part = '';

    if ($attrs && is_array($attrs)) {
        foreach ($attrs as $k => $v) {
            $part .= "{$k}='{$v}' ";
        }
    } else {
        $part = $attrs;
    }

    return <<<S
<div class="form-group {$class}">
    <label for="{$id}" {$label}>$title $star</label>
    <input type="{$type}" name="{$name}" id="{$id}" class="form-control" aria-describedby="{$name}" placeholder="{$placeholder}" value="{$value}" $part {$required}>
    $_required
</div>
S;
}


/*
 * Возвращает textarea для формы.
 * $name - передать название, перевод будет взять из /app/Modules/lang/en/f.php.
 * $idForm - если используется форма несколько раз на странице, то передайте id формы, чтобы у id у чекбоксова были оригинальные id.
 * $required - если input необязательный, то передайте null, необязательный параметр.
 * $value - передать значение, необязательный параметр.
 * $label - если он нужен, то передать true, необязательный параметр.
 * $placeholder - если нужен другой текст, то передать его, необязательный параметр.
 * $class - передайте свой класс, необязательный параметр.
 * $attrs - передайте необходимые параметры строкой или в массиве ['id' => 'test', 'data-id' => 'dataTest', 'novalidate' => ''], необязательный параметр.
 * $rows - кол-во рядов, по-умолчанию 3, необязательный параметр.
 */
function textarea($name, $idForm = false, $required = true, $value = false, $label = false, $placeholder = false, $class = false, $attrs = false, $rows = 3)
{
    $lang = lang();
    $title = Lang::has("{$lang}::f.{$name}") ? __("{$lang}::f.{$name}") : $name;
    $id = $idForm ? "{$idForm}_{$name}" : $name;
    $required = $required ? 'required' : null;
    $star = $required ? '<sup>*</sup>' : null;

    $value = $value ?: old($name) ?: null;
    $placeholderStar = $label && $required ? '*' : null;
    $placeholderLabel = !$label && $required ? '...' : null;
    $placeholder = $placeholder ?: $title . $placeholderStar . $placeholderLabel;

    $label = $label ? null : 'class="sr-only"';
    $rows = (int)$rows;
    $_required = __("{$lang}::f.required");
    $_required = $required ? "<div class=\"invalid-feedback\">{$_required}</div>" : null;
    $part = '';

    if ($attrs && is_array($attrs)) {
        foreach ($attrs as $k => $v) {
            $part .= "{$k}='{$v}' ";
        }
    } else {
        $part = $attrs;
    }

    return <<<S
<div class="form-group">
    <label for="{$id}" {$label}>$title $star</label>
    <textarea name="{$name}" id="{$id}" class="form-control {$class}" placeholder="{$placeholder}" rows="{$rows}" $part {$required}>{$value}</textarea>
    $_required
</div>
S;
}


/*
 * Возвращает select для формы.
 * $name - передать название, перевод будет взять из /app/Modules/lang/en/f.php.
 * $options - передать options, строкой, массивом или объектом (если $value будет равна одму из значений этого массива, то этот option будет selected).
 * $idForm - если используется форма несколько раз на странице, то передайте id формы, чтобы у id у чекбоксова были оригинальные id.
 * $value - передать значение, необязательный параметр.
 * $label - если он нужен, то передать true, необязательный параметр.
 * $class - передайте свой класс, необязательный параметр.
 * $attrs - передайте необходимые параметры строкой или в массиве ['id' => 'test', 'data-id' => 'dataTest', 'novalidate' => ''], необязательный параметр.
 * $disabledValue - передать значения, для которого установить атрибут disabled.
 * $option_id_value - передайте true, если передаёте массив $options, в котором ключи это id для вывода как значения для option, необязательный параметр.
 */
function select($name, $options, $idForm = null, $value = null, $label = false, $class = null, $attrs = false, $disabledValue = null, $option_id_value = null)
{
    $lang = lang();
    $title = Lang::has("{$lang}::f.{$name}") ? __("{$lang}::f.{$name}") : $name;
    $id = $idForm ? "{$idForm}_{$name}" : $name;
    $value = $value ?: old($name) ?: null;
    $label = $label ? null : 'class="sr-only"';

    // Принимает в объекте 2 параметра, первый - value для option, второй название для option
    $opts = '';
    if (is_object($options)) {
        foreach ($options as $v) {
            $i = 0;
            foreach ($v as $vv) {
                if (!$i) {
                    $selected = $value == $vv ? ' selected' : null;
                    $opts .= "<option value='{$vv}' {$selected}>";

                } else {

                    $t = Lang::has("{$lang}::t.{$vv}")  ? __("{$lang}::t.{$vv}") : $vv;
                    $opts .= "{$t}</option>\n";
                }
                $i++;
            }
        }

    } elseif (is_array($options)) {
        foreach ($options as $k => $v) {
            $selected = $value === $v ? ' selected' : null;
            $disabled = $disabledValue && $k == $disabledValue ? ' disabled' : null;
            $t = Lang::has("{$lang}::t.{$v}") ? __("{$lang}::t.{$v}") : $v;
            $v = $option_id_value ? $k : $v;
            $opts .= "<option value='{$v}' {$selected}{$disabled}>{$t}</option>\n";
        }
    } else {
        $opts = $options;
    }

    $part = '';
    if ($attrs && is_array($attrs)) {
        foreach ($attrs as $k => $v) {
            $part .= "{$k}='{$v}' ";
        }
    } else {
        $part = $attrs;
    }

    return <<<S
<div class="form-group $class">
    <label for="{$id}" {$label}>{$title}</label>
    <select class="form-control" name="{$name}" id="{$id}" {$part}>
        $opts
    </select>
</div>
S;
}


/*
 * Возвращает checkbox для формы.
 * $name - передать название, перевод будет взять из /app/Modules/lang/en/f.php.
 * $idForm - если используется форма несколько раз на странице, то передайте id формы, чтобы у id у чекбоксова были оригинальные id.
 * $required - если необязательный, то передайте null, необязательный параметр.
 * $checked - Если checkbox должен быть нажат, то передайте true, необязательный параметр.
 * $class - Передайте свой класс, необязательный параметр.
 * $title - Можно передать свой заголовок, например с ссылкой, необязательный параметр.
 */
function checkbox($name, $idForm = false, $required = true, $checked = false, $class = false, $title = false)
{
    $lang = lang();
    $_title = Lang::has("{$lang}::f.{$name}") ? __("{$lang}::f.{$name}") : $name;
    $title = $title ?: $_title;
    $id = $idForm ? "{$idForm}_{$name}" : $name;

    $checked = $checked || old($name) ? 'checked' : null;
    $required = $required ? 'required' : null;
    $_required = __("{$lang}::f.must_accept");
    $_required = $required ? "<div class=\"invalid-feedback\">{$_required}</div>" : null;

    return <<<S
<div class="{$class}">
    <div class="custom-control custom-checkbox my-3">
        <input type="checkbox" class="custom-control-input" name="{$name}" id="{$id}" $checked {$required}>
        <label class="custom-control-label" for="{$id}">{$title}</label>
        $_required
    </div>
</div>
S;
}

function checkboxSimple($name, $idForm = false, $required = true, $checked = false, $class = false, $title = false)
{
    $lang = lang();
    $_title = Lang::has("{$lang}::f.{$name}") ? __("{$lang}::f.{$name}") : $name;
    $title = $title ?: $_title;
    $id = $idForm ? "{$idForm}_{$name}" : $name;

    $checked = $checked || old($name) ? 'checked' : null;
    $required = $required ? 'required' : null;
    $_required = __("{$lang}::f.must_accept");
    $_required = $required ? "<div class=\"invalid-feedback\">{$_required}</div>" : null;

    return <<<S
<div class="form-group {$class}">
    <div class="form-check mt-3 mb-2">
        <input class="form-check-input" type="checkbox" name="{$name}" id="{$id}" $checked {$required}>
        <label class="form-check-label" for="{$id}">{$title}</label>
        $_required
    </div>
</div>
S;
}

function checkboxSwitch($name, $idForm = false, $required = true, $checked = false, $class = false, $title = false)
{
    $lang = lang();
    $_title = Lang::has("{$lang}::f.{$name}") ? __("{$lang}::f.{$name}") : $name;
    $title = $title ?: $_title;
    $id = $idForm ? "{$idForm}_{$name}" : $name;

    $checked = $checked || old($name) ? 'checked' : null;
    $required = $required ? 'required' : null;
    $_required = __("{$lang}::f.must_accept");
    $_required = $required ? "<div class=\"invalid-feedback\">{$_required}</div>" : null;

    return <<<S
<div class="{$class}">
    <div class="custom-control custom-switch my-3">
        <input type="checkbox" class="custom-control-input" name="{$name}" id="{$id}" $checked {$required}>
        <label class="custom-control-label" for="{$id}">{$title}</label>
        $_required
    </div>
</div>
S;
}


/*
 * Возвращает скрытый input для формы.
 * $name - Передать имя input.
 * $value - Значение.
 */
function hidden($name, $value)
{
    return "<input type=\"hidden\" name=\"{$name}\" value='{$value}'>";
}


/*
 * Возвращает код рекапчи от Гугл.
 * $class - Передайте свой класс, необязательный параметр.
 */
function recaptcha($class = false)
{
    $key = config('add.recaptcha_public_key');
    if (!$key) return false;

    return <<<S
<div class="{$class}">
    <div class="g-recaptcha my-3" data-sitekey="{$key}"></div>
</div>
S;
}


/*
 * Возвращает скрытый input для формы.
 * $title - Передать название для кнопки.
 * $class - Передайте свой класс, необязательный параметр.
 */
function btn($title, $class = false)
{
    if ($title) {
        $lang = lang();
        if (Lang::has("{$lang}::f.{$title}")) {
            $title = __("{$lang}::f.{$title}");

        } elseif (Lang::has("{$lang}::t.{$title}")) {
            $title = __("{$lang}::t.{$title}");
        }

        return <<<S
<button type="submit" class="btn btn-primary btn-pulse {$class}">
    <span class="spinner-grow spinner-grow-sm mr-2 js-none" role="status" aria-hidden="true"></span>
    <span>{$title}</span>
</button>
S;
    }
    return false;
}


/*
 * Возвращает модальное окно.
 * $id - передать связующее id.
 * $title - Передать название, перевод будет взять из /resources/lang/en/e.php, необязательный параметр.
 * $class - к примеру modal-lg, будет большое окно, необязательный параметр.
 * $attrs - если нужны дополнительные атрибуты, необязательный параметр.
 */
function modal($id, $title = null, $class = null, $attrs = null)
{
    $lang = lang();
    $titleLang = Lang::has("{$lang}::t.{$title}") ? __("{$lang}::t.{$title}") : $title;
    $title = $titleLang ? "<h4 class=\"modal-title mb-2\">{$titleLang}</h4>" : null;

    return <<<S
<div id="{$id}" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="{$id}" aria-hidden="true" {$attrs}>
    <div class="modal-dialog {$class}" role="document">
        <div class="modal-content px-0 px-lg-3">
            <div class="modal-header border-0 mt-2 pb-0 position-relative">
                $title
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body mb-4">
S;
}
/*
 * Если нужно разместить футер модального окна, передайте true. И в коде перед футером закройте </div>
 */
function modalEnd($footer = null)
{
    $footer = $footer ? null : '</div>';
    return <<<S
            $footer
        </div>
    </div>
</div>
S;
}


/*
 * Возвращает плеер youtube.
 * $link - ссылка на видео youtube, из кнопки поделиться (Не передавать ссылку из окна браузера).
 * Чтобы в паузе не было предложений других видео в конце ссылки добавлено ?rel=0
 */
function youtube($link) {
    if ($link) {
        return <<<S
<div class="embed-responsive embed-responsive-16by9">
    <iframe class="embed-responsive-item" src="{$link}?rel=0" allowfullscreen></iframe>
</div>
S;
    }
    return false;
}
