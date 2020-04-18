<?php


namespace App\Helpers\Admin;


class Constructor
{
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
    public static function adminMainBlock($title, $count, $icon, $link, $bottom_title = null, $mt = null, $mb = 4, $class = null)
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
    public static function adminGrayBlock($text, $link, $title = null, $icon = null, $bottom_title = null, $mt = null, $mb = 4, $class = null, $bg_dark = null)
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
    public static function adminInfoBlock($title, $count, $mt = null, $mb = 4, $class = null)
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
    * Для прилипающей кнопки запустите эту функцию, предворительно установив id для кнопки.
    * $idBtn - id кнопки, по-умолчанию btn-sticky.
    */
    public static function stickyScript($idBtn = 'btn-sticky')
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
}
