<?php


namespace App\Modules\Admin\Helpers;

use Illuminate\Support\Facades\Lang;

class Constructor
{
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
     * $id - Передайте свой id, необязательный параметр.
     * $idForm - если используется форма несколько раз на странице, то передайте id формы, чтобы у id у чекбоксова были оригинальные id.
     */
    public static function input($name, $value = null, $required = true, $type = null, $label = true, $placeholder = null, $class = null, $attrs = [], $classInput = null, $id = null, $idForm = null)
    {
        $lang = lang();
        $title = Lang::has("{$lang}::f.{$name}") ? __("{$lang}::f.{$name}") : $name;
        $id = $idForm ? "{$idForm}_{$id}" : $id;
        $id = $id ?: $name;

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

        if ($attrs) {
            foreach ($attrs as $k => $v) {
                if ($v) {
                    $part .= "{$k}='{$v}' ";
                } else {
                    $part .= "$k ";
                }

            }
        }

        return <<<S
<div class="form-group {$class}">
    <label for="{$id}" {$label}>$title $star</label>
    <input type="{$type}" name="{$name}" id="{$id}" class="form-control {$classInput}" aria-describedby="{$name}" placeholder="{$placeholder}" value="{$value}" $part {$required}>
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
     * $id - Передайте свой id, необязательный параметр.
     * $idForm - если используется форма несколько раз на странице, то передайте id формы, чтобы у id у чекбоксова были оригинальные id.
     * $htmlspecialchars - $value обёртываем в функцию htmlspecialchars, передайте false, если не надо.
     */
    public static function textarea($name, $value = null, $required = true, $label = true, $placeholder = null, $class = null, $attrs = [], $rows = 3, $id = null, $idForm = null, $htmlspecialchars = true)
    {
        $lang = lang();
        $title = Lang::has("{$lang}::f.{$name}") ? __("{$lang}::f.{$name}") : $name;
        $id = $idForm ? "{$idForm}_{$id}" : $id;
        $id = $id ?: $name;

        $required = $required ? 'required' : null;
        $star = $required ? '<sup>*</sup>' : null;

        $value = $value ?: old($name) ?: null;
        $value = $htmlspecialchars ? e($value) : $value;
        $placeholderStar = $label && $required ? '*' : null;
        $placeholderLabel = !$label && $required ? '...' : null;
        $placeholder = $placeholder ?: $title . $placeholderStar . $placeholderLabel;

        $label = $label ? null : 'class="sr-only"';
        $rows = (int)$rows;
        $_required = __("{$lang}::f.required");
        $_required = $required ? "<div class=\"invalid-feedback\">{$_required}</div>" : null;
        $part = '';
        if ($attrs) {
            foreach ($attrs as $k => $v) {
                if ($v) {
                    $part .= "{$k}='{$v}' ";
                } else {
                    $part .= "$k ";
                }
            }
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
     * $options - передать в массиве options (если $value будет равна одму из значений этого массива, то этот option будет selected).
     * $value - передать значение, необязательный параметр.
     * $label - если он нужен, то передать true, необязательный параметр.
     * $class - передайте свой класс, необязательный параметр.
     * $attrs - передайте необходимые параметры в массиве ['id' => 'test', 'data-id' => 'dataTest'], необязательный параметр.
     * $option_id_value - передайте true, если передаёте массив $options, в котором ключи это id для вывода как значения для option, необязательный параметр.
     * $translation - если не надо переводить текст option, то передать true, необязательный параметр.
     * $disabledValue - передать значения, для которого установить атрибут disabled.
     * $id - Передайте свой id, необязательный параметр.
     * $idForm - если используется форма несколько раз на странице, то передайте id формы, чтобы у id у чекбоксова были оригинальные id.
     */
    public static function select($name, $options, $value = null, $label = true, $class = null, $attrs = [], $option_id_value = null, $translation = null, $disabledValue = null, $id = null, $idForm = null)
    {
        $lang = lang();
        $title = Lang::has("{$lang}::f.{$name}") ? __("{$lang}::f.{$name}") : $name;
        $id = $idForm ? "{$idForm}_{$id}" : $id;
        $id = $id ?: $name;
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
     * $required - если необязательный, то передайте null, необязательный параметр.
     * $checked - Если checkbox должен быть нажат, то передайте true, необязательный параметр.
     * $class - Передайте свой класс, необязательный параметр.
     * $title - Можно передать свой заголовок, например с ссылкой, необязательный параметр.
     * $idForm - если используется форма несколько раз на странице, то передайте id формы, чтобы у id у чекбоксова были оригинальные id.
     */
    public static function checkbox($name, $required = true, $checked = null, $class = null, $title = null, $idForm = null)
    {
        $lang = lang();
        $_title = Lang::has("{$lang}::f.{$name}") ? __("{$lang}::f.{$name}") : $name;
        $title = $title ?: $_title;
        $id = $idForm ? "{$idForm}_{$name}" : $name;

        $checked = $checked || old($name) ? 'checked' : null;
        $required = $required ? 'required' : null;
        $_required = __("{$lang}::f.required");
        $_required = $required ? "<div class=\"invalid-feedback\">{$_required}</div>" : null;

        return <<<S
<div class="{$class}">
    <div class="custom-control custom-checkbox mt-4 mb-2">
        <input type="checkbox" class="custom-control-input" name="{$name}" id="{$id}" $checked {$required}>
        <label class="custom-control-label" for="{$id}">{$title}</label>
        $_required
    </div>
</div>
S;
    }


    /*
     * Возвращает скрытый input для формы.
     * $name - передать имя input.
     * $value - значение.
     */
    public static function hidden($name, $value)
    {
        return "<input type=\"hidden\" name=\"{$name}\" value='{$value}'>";
    }


    /*
     * Возвращает заголовок h2.
     * $title - передать заголовок.
     * $class - классы, можно любое кол-во, необязательный параметр.
     * $mb - если не нужен нижний отступ, то передать false, необязательный параметр.
     */
    public static function adminH2($title, $class = null, $mb = true)
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
    public static function adminBlockLink($title, $title_link, $link, $class = null, $attrs = null)
    {
        return <<<S
<div class="d-md-flex justify-content-between align-items-center border text-secondary rounded my-2 py-3 px-4 no-wrap">
    <div class="my-1 mr-md-2">{$title}</div>
    <a href="{$link}" class="btn btn-primary btn-sm my-1 d-block {$class}" {$attrs}>{$title_link}</a>
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
    public static function adminMainBlock($title, $count, $icon, $link, $bottom_title = null, $mt = null, $mb = 4, $class = null)
    {
        $lang = lang();
        $title = __("{$lang}::a.{$title}");
        $bottom_title = $bottom_title ?: __("{$lang}::s.read_more");
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
    public static function adminGrayBlock($text, $link, $title = null, $icon = null, $bottom_title = null, $mt = null, $mb = 4, $class = null, $bg_dark = null)
    {
        $lang = lang();
        $route = route('admin.main');
        $title = $title ? "<p class='font-weight-bold'><a href='$route/$link'>" . __("{$lang}::a.{$title}") . '</a></p>' : null;
        $icon = $icon ?: 'arrow_forward';
        $bottom_title = $bottom_title ?: __("{$lang}::s.read_more");
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
    public static function adminInfoBlock($title, $count, $mt = null, $mb = 4, $class = null)
    {
        $lang = lang();
        $title = $title ?: __("{$lang}::a.{$title}");
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
    * Для прилипающей кнопки запустите эту функцию, предворительно установив id для кнопки.
    * $idBtn - id кнопки, по-умолчанию btn-sticky.
    */
    public static function stickyScript($idBtn = 'btn-sticky')
    {
        if ($idBtn) {

            ob_start(); ?>
            <script>
                document.addEventListener('DOMContentLoaded', function() {

                    var sticky = document.getElementById('<?= $idBtn; ?>'),
                        aside = document.querySelector('aside.aside'),
                        //content = document.querySelector('.main-content'),
                        tabs = document.getElementById('tabs-edit-content'),
                        tabActive = null,
                        tabHeight = null,
                        topBlock = null,
                        bottomBlocks = null,
                        heightMain = null

                    if (tabs) {
                        tabActive = tabs.querySelector('.active')

                        if (tabActive) {
                            /*var rect = tabActive.getBoundingClientRect()
                            tabHeight = rect.height*/
                            tabHeight = tabActive.offsetHeight
                            topBlock = 38
                            bottomBlocks = 157

                            heightMain = tabHeight + topBlock + bottomBlocks
                        }
                    }


                    // Если ширина экрана больше 991рх
                    if (sticky && aside && content && document.body.clientWidth > 991) {

                        var heightWindow = window.innerHeight, // Высота окна браузера
                            //heightSticky = sticky.getBoundingClientRect().top, // Высота до блока sticky
                            heightSticky = sticky.offsetTop, // Высота от блока sticky до верха тега формы
                            heightBlock = sticky.offsetHeight, // Высота блока
                            add = 192, // Добавляемое значение от тега формы до самого верха
                            heightNewSticky = 66

                        heightSticky = heightSticky + heightBlock + add
                        heightMain = heightMain + heightBlock + add


                        function addButton() {
                            setTimeout(function () {
                                var asideWidth = aside.offsetWidth // Ширина сайдбара слева
                                //var contentLeft = window.getComputedStyle(content, null).getPropertyValue('padding-left') // У контента получить padding-left в px

                                // Отрезать px в конце строки
                                //contentLeft = contentLeft.substring(0, contentLeft.length - 2)

                                //sticky.style.paddingLeft = '34px'
                                sticky.classList.add('bg-white', 'w-100', 'position-fixed', 'z-7')
                                sticky.style.left = (asideWidth + 34) + 'px' // 82px
                                // Получисть ширину aside





                                //sticky.style.left = (Number(asideWidth) + Number(contentLeft)) + 'px'
                                sticky.style.height = heightNewSticky + 'px'
                                sticky.style.top = (heightWindow - heightNewSticky) + 'px'
                            }, 10)
                        }

                        function remove() {
                            sticky.classList.remove('bg-white', 'w-100', 'position-fixed', 'z-7')
                            sticky.style.padding = '0'
                        }

                        // Без скролла
                        if (tabs) { // Если есть табы

                            if (heightWindow < heightMain) {

                                addButton()

                            } else {

                                remove()

                            }

                        } else { // Для всех прочих

                            if (heightWindow < heightSticky) {

                                addButton()

                            } else {

                                remove()

                            }

                        }


                        // Отлеживаем скролл
                        window.addEventListener('scroll', function(e) {

                            // Если есть табы
                            if (tabs) {

                                if (pageYOffset + heightWindow < heightMain) {

                                    addButton()

                                } else {

                                    remove()

                                }

                                // Для всех прочих
                            } else {

                                if (pageYOffset + heightWindow < heightSticky) {

                                    addButton()

                                } else {

                                    remove()

                                }

                            }

                        })
                    }

                }, false)
            </script>
            <?php

            return ob_get_clean();
        }
        return false;
    }
}
