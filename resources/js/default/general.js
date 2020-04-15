
export default {
    methods: {
        /*
        * Анимация Scale.
        * show - переменная true или false.
        * el - элемент.
        * ms - время анимации в мс, необязательный параметр.
        */
        animateScale: function (show, el, ms = 200, classNone = false) {
            if (show) {
                classNone ? el.classList.remove('d-none') : null
                el.classList.remove('anime-to-center')
                classNone ? el.classList.add('anime-from-center') : el.classList.add('show', 'anime-from-center')
            } else {
                el.classList.remove('anime-from-center')
                el.classList.add('anime-to-center')
                setTimeout(function () {
                    el.classList.remove(classNone ? 'd-none' :'show')
                }, ms)
            }
        },
        move: function (item, accordion = false) {
            const move = document.querySelectorAll('.move')
            item = document.querySelector(item)

            if (item.classList.contains('shut')) {
                if (accordion) {
                    move.forEach(el => {el.classList.remove('shut')})
                } else {
                    item.classList.remove('shut')
                }
                item.classList.add('open')

            } else {
                if (accordion) {
                    move.forEach(el => {el.classList.remove('open')})
                } else {
                    item.classList.remove('open')
                }
                item.classList.add('shut')
            }
        },

        // Первая буква заглавная
        ucFirst: function (str) {
            return str.substr(0,1).toUpperCase() + str.substr(1)
        },

        // Заменяет в строке все _ на -
        snake_case: function (str) {
            return str.replace(/-/g, '_')
        }
    }
}
