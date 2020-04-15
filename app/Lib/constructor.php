<?php

/*
 * Возвращает картинку svg вместе с заменяющей её картинкой.
 * $img - путь с названием картинки.
 * $alt - текст для картинки.
 * $width - ширина для картинки, не забудьте написать единицы измерения, необязательный параметр.
 * $img_svg - путь с названием картинки, svg файл, если не передавать, то возьмётся $img, необязательный параметр.
 * $class - класс для тега img, необязательный параметр.
 * $id - id для тега img, необязательный параметр.
 */
function svg($img, $alt = null, $width = null, $img_svg = null, $class = null, $id = null) {
    $width = $width ? " style='width: $width'" : null;
    $path = pathinfo($img);
    $path_dir = $path['dirname'] === '.' ? null : $path['dirname'] . '/';
    $img_svg = $img_svg ?: "$path_dir{$path['filename']}.svg";
    $id = $id ? "id='$id'" : null;
    $alt = $alt ? \App\Helpers\Str::removeTag($alt) : ' ';

    if (is_file(config('add.imgPath') . "/$img") && is_file(config('add.imgPath') . "/$img_svg")) {
        $img = asset(config('add.img') . "/$img");
        $img_svg = asset(config('add.img') . "/$img_svg");

        return <<<S

        <picture>
            <source srcset="$img" type="image/svg+xml">
            <img src="$img_svg" class="responsive-img $class" $id alt="$alt" $width>
        </picture>

S;
    }
    return false;
}


/*
 * Возвращает input для формы.
 * $name - передать название, перевод будет взять из /resources/lang/en/f.php.
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
    $title = __("f.$name");
    $required = $required ? 'required' : null;
    $type = $type ? $type : 'text';
    $star = $required ? '<sup>*</sup>' : null;
    $value = $value ?? old($name) ?? null;
    $label = $label ? null : 'class="sr-only"';
    $placeholderStar = $label && $required ? '*' : null;
    $placeholderLabel = !$label && $required ? '...' : null;
    $placeholder = $placeholder ?: $title . $placeholderStar . $placeholderLabel;
    $_required = __('f.required');
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
 * $name - передать название, перевод будет взять из /resources/lang/en/f.php.
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
    $title = __("f.$name");
    $required = $required ? 'required' : null;
    $star = $required ? '<sup>*</sup>' : null;
    $label = $label ? null : 'class="sr-only"';

    $value = $value ?: old($name) ?: null;
    $placeholderStar = $label && $required ? '*' : null;
    $placeholderLabel = !$label && $required ? '...' : null;
    $placeholder = $placeholder ? $title . $placeholderStar . $placeholderLabel  : null;
    //$value = $value ?: $placeholder;

    $rows = (int)$rows;
    $_required = __('f.required');
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
 * $name - передать название, перевод будет взять из /resources/lang/en/f.php.
 * $options - передать в массиве options (если $value будет равна одму из значений этого массива, то этот option будет selected).
 * $value - передать значение, необязательный параметр.
 * $label - если он нужен, то передать true, необязательный параметр.
 * $class - передайте свой класс, необязательный параметр.
 * $attrs - передайте необходимые параметры в массиве ['id' => 'test', 'data-id' => 'dataTest'], необязательный параметр.
 * $option_id_value - передайте true, если передаёте массив $options, в котором ключи это id для вывода как значения для option, необязательный параметр.
 * $translation - если не надо переводить текст option, то передать true, необязательный параметр.
 */
function select($name, $options, $value = null, $label = true, $class = null, $attrs = [], $option_id_value = null, $translation = null)
{
    $title = __("f.$name");
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

                    $t = $translation ? $vv : __("s.{$vv}");
                    $opts .= "{$t}</option>\n";
                }
                $i++;
            }
        }
    } elseif (is_array($options)) {
        $opts = '';
        foreach ($options as $k => $v) {
            $selected = $value === $v ? ' selected' : null;
            $t = __("s.$v");
            $t = $translation ? $v : __("s.{$v}");
            $v = $option_id_value ? $k : $v;
            $opts .= "<option value='{$v}' {$selected}>{$t}</option>\n";
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
 * $name - передать название, перевод будет взять из /resources/lang/en/f.php.
 * $checked - Если checkbox должен быть нажат, то передайте true, необязательный параметр.
 * $class - Передайте свой класс, необязательный параметр.
 * $id - Передайте свой id, необязательный параметр.
 * $title - Можно передать свой заголовок, например с ссылкой, необязательный параметр.
 */
function checkbox($name, $required = true, $checked = null, $class = null, $id = null, $title = null)
{
    $title = $title ?: __("f.$name");
    $id = $id ?: $name;
    $checked = $checked || old($name) ? 'checked' : null;
    $required = $required ? 'required' : null;
    $_required = __('f.required');
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
 * Возвращает заголовок h2.
 * $title - передать заголовок.
 * $class - классы, можно любое кол-во, необязательный параметр.
 * $mb - если не нужен нижний отступ, то передать false, необязательный параметр.
 */
function adminH2($title, $class = null, $mb = true)
{
    $mb = $mb ?: 'mb-4';
    return <<<S
<div class="row">
        <div class="col">
            <h2 class="font-weight-light text-secondary $mb {$class}">{$title}</h2>
        </div>
    </div>
S;
}


/*
 * Возвращает блок с ссылкой.
 * $title - передать заголовок.
 * $title_link - передать заголовок для ссылки.
 * $link - передать ссылку.
 * $class - классы, можно любое кол-во, необязательный параметр.
 */
function adminBlockLink($title, $title_link, $link, $class = null, $attrs = null)
{
    return <<<S
<div class="d-flex justify-content-between align-items-center flex-wrap border text-secondary rounded my-2 py-3 px-4">
            <span class="my-1">{$title}</span>
            <a href="{$link}" class="btn btn-primary btn-sm my-1 {$class}" {$attrs}>{$title_link}</a>
        </div>
S;
}


/*
 * Блок с ссылкой и информацией.
 * $title - название блока.
 * $count - кол-во для вывода.
 * $icon - название иконки.
 * $link - ссылка, в формате 'page/add' - для перехода на добавление страницы.
 * $bottom_title - нижняя надпись кнопки перехода, передать ключ перевода, необязательный параметр.
 * $mt - отстут сверху, по-умолчанию нет, необязательный параметр.
 * $mb - отстут снизу, по-умолчанию 4, необязательный параметр.
 * $class - добавьте свой класс, необязательный параметр.
 */
function adminMainBlock($title, $count, $icon, $link, $bottom_title = null, $mt = null, $mb = 4, $class = null)
{
    $title = __("a.$title");
    $bottom_title = $bottom_title ?: __('c.read_more');
    $count = $count ?: '0';
    $mt = $mt ? "mt-$mt": null;
    $mb = $mb ? "mb-$mb": null;
    $route = route('admin.main');

    return <<<S
<div class="col-xl-3 col-lg-6 info-block $mt $mb {$class}">
    <div class="card h-100 text-white border-0">
        <div class="card-body position-relative py-4 cur" onclick="location.href='{$route}/{$link}';">
            <div>
                <h4 class="font-weight-bold">{$count}</h4>
                <p class="text-uppercase position-relative mb-0 z">{$title}</p>
            </div>
            <i aria-hidden="true" class="material-icons">{$icon}</i>
        </div>
        <div class="card-footer small p-1">
            <a href="{$route}/{$link}" class="d-flex justify-content-center align-items-center font-weight-light text-white">
            <span>{$bottom_title}</span>
            <i aria-hidden="true" class="material-icons ml-1">arrow_forward</i></a>
        </div>
    </div>
</div>
S;
}


/*
 * Блок с ссылкой и информацией.
 * $text - текст, можно передать в нужных тегах или передать null, необязательный параметр.
 * $link - ссылка, в формате 'page/add' - для перехода на добавление страницы.
 * $title - название блока.
 * $icon - название иконки, необязательный параметр.
 * $bottom_title - нижняя надпись кнопки перехода, необязательный параметр.
 * $mt - отстут сверху, по-умолчанию нет, необязательный параметр.
 * $mb - отстут снизу, по-умолчанию 4, необязательный параметр.
 * $class - добавьте свой класс, необязательный параметр.
 */
function adminGrayBlock($text, $link, $title = null, $icon = null, $bottom_title = null, $mt = null, $mb = 4, $class = null, $bg_dark = null)
{
    $route = route('admin.main');
    $title = $title ? "<p class='font-weight-bold'><a href='$route/$link'>" . __("a.$title") . '</a></p>' : null;
    $icon = $icon ?: 'arrow_forward';
    $bottom_title = $bottom_title ?: __('c.read_more');
    $mt = $mt ? "mt-$mt": null;
    $mb = $mb ? "mb-$mb": null;
    $bg_dark = $bg_dark ? 'bg-primary text-white a-light' : null;

    return <<<S
<div class="col-xl-3 col-lg-6 gray-block $mt $mb {$class}">
    <div class="card h-100 {$bg_dark}">
        <div class="card-body">
            $title
            <div>{$text}</div>
        </div>
        <div class="card-footer text-center small p-1">
            <a href="{$route}/{$link}" class="d-flex justify-content-center align-items-center  font-weight-bold">{$bottom_title} <i aria-hidden="true" class="material-icons ml-1">{$icon}</i></a>
        </div>
    </div>
</div>
S;
}


/*
 * Блок с информацией.
 * $title - название блока.
 * $count - кол-во для вывода.
 * $mt - отстут сверху, по-умолчанию нет, необязательный параметр.
 * $mb - отстут снизу, по-умолчанию 4, необязательный параметр.
 * $class - добавьте свой класс, необязательный параметр.
 */
function adminInfoBlock($title, $count, $mt = null, $mb = 4, $class = null)
{
    $title = $title ?: __("a.$title");
    $count = $count ?: '0';
    $mt = $mt ? "mt-$mt": null;
    $mb = $mb ? "mb-$mb": null;

    return <<<S
<div class="col-xl-4 col-lg-6 $mt $mb $class">
    <div class="card text-secondary border-light">
    <span class="card-body d-flex align-items-center justify-content-between py-3">
        <span>{$title}</span>
        <span class="badge badge-primary badge-pill text-white">{$count}</span>
    </span>
    </div>
</div>
S;
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
    $close = __('s.Close');

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
    $close = __('sh.continue_shopping');
    //$close = $orderBtn ? __('sh.continue_shopping') : __('s.Close');
    $makeOrder = __('sh.make_an_order');
    $routeCart = route('cart');
    $orderBtn = $orderBtn ? "<a href=\"{$routeCart}\" class=\"btn btn-primary\">{$makeOrder}</a>" : null;

    return <<<S
<div class="modal-footer border-0">
        <button type="button" class="btn btn-outline-dark" data-dismiss="modal">{$close}</button>
        $orderBtn
</div>\n
S;
}


/*
 * Для прилипающей кнопки запустите эту функцию, предворительно установив id для кнопки.
 * $idBtn - id кнопки, по-умолчанию btn-sticky.
 */
function stickyScript($idBtn = 'btn-sticky')
{
    /*if ($idBtn) {
        ob_start();

        */?><!--
        <script>
            document.addEventListener('DOMContentLoaded', function() {

                var sticky = document.getElementById('<?/*= $idBtn; */?>'),
                    aside = document.querySelector('.aside'),
                    content = document.querySelector('.main-content')

                // Если ширина экрана больше 991рх
                if (sticky && aside && content && document.body.clientWidth > 991) {

                    var heightWindow = window.innerHeight, // Высота окна браузера
                        //heightSticky = sticky.getBoundingClientRect().top, // Высота до блока sticky
                        heightSticky = sticky.offsetTop, // Высота до блока sticky
                        heightBlock = sticky.offsetHeight, // Высота блока
                        add = 140 // Добавляемое значение

                    // Отлеживаем скролл
                    window.addEventListener('scroll', function(e) {

                        if (pageYOffset + heightWindow + add < heightSticky) {
                            var asideWidth = aside.offsetWidth, // Ширина сайдбара слева
                                contentLeft = window.getComputedStyle(content, null).getPropertyValue('padding-left') // У контента получить padding-left в px

                            // Отрезать px в конце строки
                            contentLeft = contentLeft.substring(0, contentLeft.length - 2)

                            sticky.classList.add('bg-white', 'w-100', 'position-fixed', 'z-7')
                            sticky.style.left = (Number(asideWidth) + Number(contentLeft)) + 'px'
                            sticky.style.padding = '.5rem 0 1.6rem 2.4rem'
                            sticky.style.top = (heightWindow - (add / 2)) + 'px'

                        } else {
                            sticky.classList.remove('bg-white', 'w-100', 'position-fixed', 'z-7')
                            sticky.style.padding = '0'
                        }

                    })
                }

            }, false)
        </script>
        --><?php
/*
        return ob_get_clean();
    }*/
    return false;
}
