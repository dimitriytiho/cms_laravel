
// Функция после загрузки страницы
document.addEventListener('DOMContentLoaded', function() {

    const offset = 20
    const heightScreen = window.innerHeight

    const arrowTop = document.querySelector('#arrow-top')
    const animeTitle = document.querySelectorAll('.title')
    const aboutItem = document.querySelectorAll('.about__item')
    const pricesItem = document.querySelectorAll('.prices__main--item')


    // Код со скролом
    window.onscroll = function() {
        let scrollTop = window.pageYOffset || document.documentElement.scrollTop


        // Кнопка вверх
        /*if (scrollTop > 300 && !arrowTop.classList.contains('scale-in')) {
            arrowTop.classList.remove('scale-out')
            arrowTop.classList.add('scale-in')
        } else if (scrollTop < 300 && !arrowTop.classList.contains('scale-out')) {
            arrowTop.classList.remove('scale-in')
            arrowTop.classList.add('scale-out')
        }*/


        // Добавление класса анимации для lg дисплеев (или других, можно выбрать)
        /*addAnimate(animeTitle) // Вызовите функцию и передайте нужный селектор, который получите выше чем window.onscroll
        addAnimate(aboutItem, 'animate-bottom')
        addAnimate(pricesItem, 'animate-bottom')

        function addAnimate(selectorAll, addClassName = 'animate-right', widthScreenAfter = 992) {
            if (widthScreen > widthScreenAfter) {
                selectorAll.forEach(function (el) {
                    const elTop = el.offsetTop
                    const elHeight = el.offsetHeight
                    if (heightScreen + scrollTop - offset > elTop && scrollTop + offset < elTop + elHeight) {
                        el.classList.add(addClassName)
                    }
                })
            }
        }*/

    }

}, false)
