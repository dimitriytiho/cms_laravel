<?php

use App\Helpers\Str;


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

    if (is_file(config('add.imgPath') . "/$img") && is_file(config('add.imgPath') . "/$img_svg")) {
        $img = asset(config('add.img') . "/$img");
        $img_svg = asset(config('add.img') . "/$img_svg");

        return <<<S
        <picture {$classPicture}>
            <source srcset="{$img}" type="image/svg+xml">
            <img src="{$img_svg}" class="responsive-img {$class}" $id alt="{$alt}" {$width}>
        </picture>
S;
    }
    return false;
}


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
 * Возвращает input для формы.
 * $name - передать название, перевод будет взять из /app/Modules/lang/en/f.php.
 * $value - передать значение, необязательный параметр.
 * $required - если input необязательный, то передайте null, необязательный параметр.
 * $type - тип input, по-умолчанию text, необязательный параметр.
 * $label - если он нужен, то передать true, необязательный параметр.
 * $placeholder - если нужен другой текст, то передать его, необязательный параметр.
 * $class - передайте свой класс, необязательный параметр.
 * $attrs - передайте необходимые параметры в массиве ['id' => 'test', 'data-id' => 'dataTest', 'novalidate' => ''], необязательный параметр.
 * $classInput - передайте свой класс для input, необязательный параметр.
 */
function input($name, $value = null, $required = true, $type = null, $label = true, $placeholder = null, $class = null, $attrs = [], $classInput = null)
{
    $lang = lang();
    $title = Lang::has("{$lang}::f.{$name}") ? __("{$lang}::f.{$name}") : $name;
    $required = $required ? 'required' : null;
    $type = $type ? $type : 'text';
    $star = $required ? '<sup>*</sup>' : null;
    $value = $value ?? old($name) ?? null;
    $label = $label ? null : 'class="sr-only"';
    $placeholderStar = $label && $required ? '*' : null;
    $placeholderLabel = !$label && $required ? '...' : null;
    $placeholder = $placeholder ?: $title . $placeholderStar . $placeholderLabel;
    $_required = __("{$lang}::f.required");
    $_required = $required ? "<div class=\"invalid-feedback\">{$_required}</div>" : null;
    $part = '';
    if ($attrs) {
        foreach ($attrs as $k => $v) {
            if ($v) {
                $part .= "$k='$v' ";
            } else {
                $part .= "$k ";
            }

        }
    }

    return <<<S
<div class="form-group {$class}">
        <label for="{$name}" {$label}>$title $star</label>
        <input type="{$type}" name="{$name}" class="form-control {$classInput}" aria-describedby="{$name}" placeholder="{$placeholder}" value="{$value}" $part {$required}>
        $_required
    </div>
S;
}


/*
 * Возвращает textarea для формы.
 * $name - передать название, перевод будет взять из /app/Modules/lang/en/f.php.
 * $value - передать значение, необязательный параметр.
 * $required - если input необязательный, то передайте null, необязательный параметр.
 * $label - если он нужен, то передать true, необязательный параметр.
 * $placeholder - если нужен другой текст, то передать его, необязательный параметр.
 * $class - передайте свой класс, необязательный параметр.
 * $attrs - передайте необходимые параметры в массиве ['id' => 'test', 'data-id' => 'dataTest', 'novalidate' => ''], необязательный параметр.
 * $rows - кол-во рядов, по-умолчанию 3, необязательный параметр.
 */
function textarea($name, $value = null, $required = true, $label = true, $placeholder = null, $class = null, $attrs = [], $rows = 3)
{
    $lang = lang();
    $title = Lang::has("{$lang}::f.{$name}") ? __("{$lang}::f.{$name}") : $name;
    $required = $required ? 'required' : null;
    $star = $required ? '<sup>*</sup>' : null;
    $label = $label ? null : 'class="sr-only"';

    $value = $value ?: old($name) ?: null;
    $placeholderStar = $label && $required ? '*' : null;
    $placeholderLabel = !$label && $required ? '...' : null;
    $placeholder = $placeholder ? $title . $placeholderStar . $placeholderLabel  : null;
    //$value = $value ?: $placeholder;

    $rows = (int)$rows;
    $_required = __("{$lang}::f.required");
    $_required = $required ? "<div class=\"invalid-feedback\">{$_required}</div>" : null;
    $part = '';
    if ($attrs) {
        foreach ($attrs as $k => $v) {
            if ($v) {
                $part .= "$k='$v' ";
            } else {
                $part .= "$k ";
            }
        }
    }

    return <<<S
<div class="form-group">
        <label for="{$name}" {$label}>$title $star</label>
        <textarea name="{$name}" class="form-control {$class}" placeholder="{$placeholder}" rows="{$rows}" $part {$required}>{$value}</textarea>
        $_required
    </div>
S;
}


/*
 * Возвращает select для формы.
 * $name - передать название, перевод будет взять из /app/Modules/lang/en/f.php.
 * $options - передать в массиве options (если $value будет равна одму из значений этого массива, то этот option будет selected).
 * $value - передать значение, необязательный параметр.
 * $label - если он нужен, то передать true, необязательный параметр.
 * $class - передайте свой класс, необязательный параметр.
 * $attrs - передайте необходимые параметры в массиве ['id' => 'test', 'data-id' => 'dataTest'], необязательный параметр.
 * $option_id_value - передайте true, если передаёте массив $options, в котором ключи это id для вывода как значения для option, необязательный параметр.
 * $translation - если не надо переводить текст option, то передать true, необязательный параметр.
 * $disabledValue - передать значения, для которого установить атрибут disabled.
 */
function select($name, $options, $value = null, $label = true, $class = null, $attrs = [], $option_id_value = null, $translation = null, $disabledValue = null)
{
    $lang = lang();
    $title = Lang::has("{$lang}::f.{$name}") ? __("{$lang}::f.{$name}") : $name;
    $value = $value ?: old($name) ?: null;
    $label = $label ? null : 'class="sr-only"';

    // Принимает в объекте 2 параметра, первый - value для option, второй название для option
    if (is_object($options)) {
        $opts = '';
        foreach ($options as $v) {
            $i = 0;
            foreach ($v as $vv) {
                if (!$i) {
                    $selected = $value == $vv ? ' selected' : null;
                    $opts .= "<option value='{$vv}' {$selected}>";

                } else {

                    $t = $translation ? $vv : __("{$lang}::s.{$vv}");
                    $opts .= "{$t}</option>\n";
                }
                $i++;
            }
        }
    } elseif (is_array($options)) {
        $opts = '';
        foreach ($options as $k => $v) {
            $selected = $value === $v ? ' selected' : null;
            $disabled = $disabledValue && $k == $disabledValue ? ' disabled' : null;
            $t = $translation ? $v : __("{$lang}::s.{$v}");
            $v = $option_id_value ? $k : $v;
            $opts .= "<option value='{$v}' {$selected}{$disabled}>{$t}</option>\n";
        }
    } else {
        return false;
    }

    $part = '';
    if ($attrs) {
        foreach ($attrs as $k => $v) {
            $part .= "$k='$v' ";
        }
    }

    return <<<S
<div class="form-group $class">
        <label for="{$name}" {$label}>{$title}</label>
        <select class="form-control" name="{$name}" {$part}>
            $opts
        </select>
    </div>
S;
}


/*
 * Возвращает checkbox для формы.
 * $name - передать название, перевод будет взять из /app/Modules/lang/en/f.php.
 * $required - если необязательный, то передайте null, необязательный параметр.
 * $checked - Если checkbox должен быть нажат, то передайте true, необязательный параметр.
 * $class - Передайте свой класс, необязательный параметр.
 * $id - Передайте свой id, необязательный параметр.
 * $title - Можно передать свой заголовок, например с ссылкой, необязательный параметр.
 */
function checkbox($name, $required = true, $checked = null, $class = null, $id = null, $title = null)
{
    $lang = lang();
    $titleLang = Lang::has("{$lang}::f.{$name}") ? __("{$lang}::f.{$name}") : $name;
    $title = $title ?: $titleLang;
    $id = $id ?: $name;
    $checked = $checked || old($name) ? 'checked' : null;
    $required = $required ? 'required' : null;
    $_required = __("{$lang}::f.required");
    $_required = $required ? "<div class=\"invalid-feedback\">{$_required}</div>" : null;

    return <<<S
<div class="custom-control custom-checkbox mt-4 mb-2 {$class}">
        <input type="checkbox" class="custom-control-input" name="{$name}" id="{$id}" $checked {$required}>
        <label class="custom-control-label" for="{$name}">{$title}</label>
        $_required
    </div>
S;
}


/*
 * Возвращает скрытый input для формы.
 * $name - передать имя input.
 * $value - значение.
 */
function hidden($name, $value)
{
    return "<input type=\"hidden\" name=\"{$name}\" value='{$value}'>";
}


/*
 * Возвращает конструкцию модального окна, без кнопки запуска.
 * $id - id, которое указали в кнопки для модального окна.
 * $title - заголовок модального окна.
 * $body - содержание модального окна, необязательный параметр.
 * $class - к примеру modal-lg, будет большое окно, необязательный параметр.
 * $attrs - если нужны дополнительные атрибуты, необязательный параметр.
 */
function modal($id, $title, $body = null, $class = null, $attrs = null) {
    $lang = lang();
    $close = __("{$lang}::s.Close");

    return <<<S
<div class="modal fade" id="{$id}" tabindex="-1" role="dialog" aria-labelledby="{$id}" aria-hidden="true" {$attrs}>
    <div class="modal-dialog {$class}" role="document">
        <div class="modal-content px-3">
            <div class="modal-header border-0 mt-3">
                <h4 class="modal-title">{$title}</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="{$close}">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body mb-3">
                $body
            </div>
        </div>
    </div>
</div>\n
S;
}


function modalFooter($orderBtn = true) {
    $lang = lang();
    $close = __("{$lang}::sh.continue_shopping");
    //$close = $orderBtn ? __("{$lang}::sh.continue_shopping") : __("{$lang}::s.Close");
    $makeOrder = __("{$lang}::sh.make_an_order");
    $routeCart = route('cart');
    $orderBtn = $orderBtn ? "<a href=\"{$routeCart}\" class=\"btn btn-primary\">{$makeOrder}</a>" : null;

    return <<<S
<div class="modal-footer border-0">
        <button type="button" class="btn btn-outline-dark" data-dismiss="modal">{$close}</button>
        $orderBtn
</div>\n
S;
}
