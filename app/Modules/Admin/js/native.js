// import bsn from './index'

document.addEventListener('DOMContentLoaded', function() {

    // При collapse с классом .change-icon изменение иконки вверх вниз
    const changeIcon = document.querySelectorAll('.change-icon')
    if (changeIcon[0]) {

        changeIcon.forEach(function (el) {
            const collapseEl = document.getElementById(el.getAttribute('aria-controls'))

            // Собыите открытие collapse
            collapseEl.addEventListener('show.bs.collapse', function(event) {
                const icon = event.target.closest('.row').querySelector('i.fas')

                // Меняем иконку
                icon.textContent = 'fa-angle-up'

            }, false)

            // Собыите закрытие collapse
            collapseEl.addEventListener('hide.bs.collapse', function(event) {
                const icon = event.target.closest('.row').querySelector('i.fas')

                // Меняем иконку
                icon.textContent = 'fa-angle-down'

            }, false)
        })
    }


}, false)
