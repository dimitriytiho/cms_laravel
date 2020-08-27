import cookie from './cookie'
import funcs from './functions'

// Функция после загрузки страницы
document.addEventListener('DOMContentLoaded', function() {

    const aside = document.querySelector('.aside')
    if (aside) {


        let asideWidthChange = document.querySelectorAll('.aside-width-change'),
            asideMarginChange = document.querySelectorAll('.aside-margin-change'),
            asideWidthSave = cookie.getCookie(main.siteName + '_asideWidth'),
            asideIconSelector = document.querySelector('.aside-width'),
            url = location.href,
            closeMenu = true

        // cookie.setCookie(main.siteName + '_asideWidth', main.asideWidthIcon)
        // cookie.getCookie(main.siteName + '_asideWidth')

        // Если есть сохранённое значение
        if (asideWidthSave) {
            asideWidthChange.forEach(function (el) {
                el.style.width = asideWidthSave
            })

            // Если ширина для текста
            if (asideWidthSave === main.asideWidthText) {

                if (asideIconSelector) {
                    aside.classList.add('open')
                    asideIconSelector.classList.add('open')
                }


            // Если ширина для иконок
            } else if (asideWidthSave === main.asideWidthIcon) {
                if (asideIconSelector) {
                    aside.classList.remove('open')
                    asideIconSelector.classList.remove('open')
                }
            }

            if (asideMarginChange) {
                asideMarginChange.forEach(function (el) {
                    el.style.marginLeft = asideWidthSave
                })
            }

        } else {
            asideWidthChange.forEach(function (el) {
                el.style.width = main.asideWidthIcon
            })
            if (asideIconSelector) {
                aside.classList.remove('open')
                asideIconSelector.classList.remove('open')
            }
        }

        // При клике на на .aside-width меняется ширина сайдбара
        document.addEventListener('click', function(e) {

            if (document.body.clientWidth > 768) {

                // Для десктопов
                if (e.target.classList.contains('aside-width')) {

                    // Если есть класс .open
                    if (e.target.classList.contains('open')) {
                        aside.classList.remove('open')
                        e.target.classList.remove('open')

                        asideWidthChange.forEach(function (el) {
                            el.style.width = main.asideWidthIcon
                        })
                        if (asideMarginChange) {
                            asideMarginChange.forEach(function (el) {
                                el.style.marginLeft = asideWidthSave
                            })
                        }

                        cookie.setCookie(main.siteName + '_asideWidth', main.asideWidthIcon)
                        //localStorage.setItem('asideWidth', main.asideWidthIcon)


                    // Если нет класса .open
                    } else {
                        aside.classList.add('open')
                        e.target.classList.add('open')

                        asideWidthChange.forEach(function (el) {
                            el.style.width = main.asideWidthText
                        })
                        if (asideMarginChange) {
                            asideMarginChange.forEach(function (el) {
                                el.style.marginLeft = main.asideWidthText
                            })
                        }
                        cookie.setCookie(main.siteName + '_asideWidth', main.asideWidthText)
                        //localStorage.setItem('asideWidth', main.asideWidthText)
                    }
                }

            } else {

                // Для мобильных
                if (e.target.classList.contains('aside-width')) {
                    e.preventDefault()
                    const menuMobile = document.getElementById('menu-mobile')
                    closeMenu = !closeMenu

                    if (closeMenu) {
                        menuMobile.style.display = 'none'
                    } else {
                        menuMobile.style.display = 'block'
                    }
                }
            }
        })


        // Добавление активного элемента для главной
        //if (main.url === url) url += '/'
        /*const dashboardLink = aside.querySelector('a[data-title=Main]')
        if (url === main.url + '/' && dashboardLink) {
            dashboardLink.classList.add('active')
        }

        // Добавление активного элемента
        asideA.forEach(function (el) {
        const asideTitle = funcs.snake(el.dataset.title) // Приводим к snake-case
        //const asideTitle = el.dataset.title.toLowerCase() // К нижнему регистру

            // Если url содержит title, то добавить класс active
            if (url.indexOf(asideTitle) + 1) {
                el.classList.add('active')
                return false;
            }

            //if (url === el.href) {
                //el.classList.add('active')
                //return false;
            //}
        })*/

    }

}, false)
