// import bsn from './index'

document.addEventListener('DOMContentLoaded', function() {

    // При collapse с классом .change-icon изменение иконки вверх вниз
    const changeIcon = document.querySelectorAll('.change-icon')
    if (changeIcon[0]) {

        changeIcon.forEach(function (el) {
            const collapseEl = document.getElementById(el.getAttribute('aria-controls'))

            // Собыите открытие collapse
            collapseEl.addEventListener('show.bs.collapse', function(event) {
                const icon = event.target.closest('.row').querySelector('i.material-icons')

                // Меняем иконку
                icon.textContent = 'keyboard_arrow_up'

            }, false)

            // Собыите закрытие collapse
            collapseEl.addEventListener('hide.bs.collapse', function(event) {
                const icon = event.target.closest('.row').querySelector('i.material-icons')

                // Меняем иконку
                icon.textContent = 'keyboard_arrow_down'

            }, false)
        })
    }


}, false)
