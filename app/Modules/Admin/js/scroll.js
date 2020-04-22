import f from './functions'

// Функция после загрузки страницы
document.addEventListener('DOMContentLoaded', function() {

    const ms = 200

    // Кнопка вверх
    if (typeof window === 'undefined') return
    window.addEventListener('scroll', () => {
        const btn = document.querySelector('#btn-up')
        let top = window.pageYOffset || document.documentElement.offsetTop || 0

        if (top > 300) {
            btn.style.display = 'block'
            btn.classList.remove('anime-to-center')
            btn.classList.add('anime-from-center')
        } else {
            btn.classList.remove('anime-from-center')
            btn.classList.add('anime-to-center')
            setTimeout(function () {
                btn.style.display = 'none'
            }, ms)
        }
    })


    // При клике на кнопку вверх
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('btn-up-click')) {
            f.scrollUp()
        }
    })

}, false)
