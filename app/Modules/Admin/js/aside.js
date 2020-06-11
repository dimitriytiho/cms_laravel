import cookie from './cookie'
import funcs from './functions'

// Функция после загрузки страницы
document.addEventListener('DOMContentLoaded', function() {

    const aside = document.querySelector('.aside')
    if (aside) {

        // Для мобильных
        if (document.body.clientWidth < 768) {

            aside.classList.add('d-none')

            const headerIcon = document.getElementById('header__icon')
            headerIcon.classList.remove('d-flex')
            headerIcon.classList.add('d-none')

            //document.querySelector('#app main.container-fluid').classList.add('px-0')

        }

        const ms = 300,
        asideA = aside.querySelectorAll('.aside__a')

        let asideWidth = true,
            asideWidthChange = document.querySelectorAll('.aside-width-change'),
            asideMarginChange = document.querySelectorAll('.aside-margin-change'),
            asideWidthSave = cookie.getCookie('asideWidth'),
            //asideWidthSave = localStorage.getItem('asideWidth'),
            asideText = document.querySelectorAll('.aside-text'),
            asideIconSelector = document.querySelector('.aside-width'),
            url = location.href,
            closeMenu = true

        // cookie.setCookie('asideWidth', main.asideWidthIcon)
        // cookie.getCookie('asideWidth')

        // Если есть сохранённое значение
        if (asideWidthSave) {
            asideWidthChange.forEach(function (el) {
                el.style.width = asideWidthSave
            })

            // Если ширина для текста
            if (asideWidthSave === main.asideWidthText) {
                asideText.forEach(function (el) {
                    el.style.display = 'inline'
                })

                if (asideIconSelector) {
                    asideIconSelector.classList.add('open')
                }


            // Если ширина для иконок
            } else if (asideWidthSave === main.asideWidthIcon) {
                if (asideIconSelector) {
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
                el.style.width = main.asideWidthText
            })
            if (asideIconSelector) {
                asideIconSelector.classList.add('open')
            }
        }

        // При клике на на .aside-width меняется ширина сайдбара
        document.addEventListener('click', function(e) {

            if (document.body.clientWidth > 768) {

                // Для десктопов
                if (e.target.classList.contains('aside-width')) {

                    // Если есть класс .open
                    if (e.target.classList.contains('open')) {
                        e.target.classList.remove('open')

                        asideWidthChange.forEach(function (el) {
                            el.style.width = main.asideWidthIcon
                        })
                        if (asideMarginChange) {
                            asideMarginChange.forEach(function (el) {
                                el.style.marginLeft = asideWidthSave
                            })
                        }
                        asideText.forEach(function (el) {
                            el.style.display = 'none'
                        })
                        cookie.setCookie('asideWidth', main.asideWidthIcon)
                        //localStorage.setItem('asideWidth', main.asideWidthIcon)


                    // Если нет класса .open
                    } else {
                        e.target.classList.add('open')

                        asideWidthChange.forEach(function (el) {
                            el.style.width = main.asideWidthText
                        })
                        if (asideMarginChange) {
                            asideMarginChange.forEach(function (el) {
                                el.style.marginLeft = main.asideWidthText
                            })
                        }
                        cookie.setCookie('asideWidth', main.asideWidthText)
                        //localStorage.setItem('asideWidth', main.asideWidthText)
                        setTimeout(function () {
                            asideText.forEach(function (el) {
                                el.style.display = 'inline'
                            })
                        }, ms)
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
