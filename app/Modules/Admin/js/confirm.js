
// Функция после загрузки страницы
document.addEventListener('DOMContentLoaded', function() {

    // При отправки формы с .confirm-form будет подтвержение отправки
    $(document).on('submit', '.confirm-link', function(e) {
        e.preventDefault()
        var modal = $('#modal-confirm'),
            btnOk = modal.find('.btn-outline-primary')

        // Открыть модальное окно
        modal.show()

        // Отлеживаем клик по кнопке Ок
        btnOk.click(function () {

            // Закрыть модальное окно
            modal.modal('hide')

            // Включить спинер
            spinner.style.display = 'block'

            // Отправить форму
            e.target.submit()
        }.bind(e))
    })


    // При клике по ссылке .confirm-link будет подтвержение отправки (добавить атрибуты data-toggle="modal" data-target="#modal-confirm")
    $(document).on('click', '.confirm-link', function(e) { // Событие двойной клик dblclick
        e.preventDefault()
        var modal = $('#modal-confirm'),
            btnOk = modal.find('.btn-outline-primary'),
            href = e.target.href

        // Открыть модальное окно
        modal.show()

        // Отлеживаем клик по кнопке Ок
        btnOk.click(function () {

            // Закрыть модальное окно
            modal.modal('hide')

            // Включить спинер
            spinner.style.display = 'block'

            // Переход по ссылке
            document.location.href = href
        })
    })

}, false)
