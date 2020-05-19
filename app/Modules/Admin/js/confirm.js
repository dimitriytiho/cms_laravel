
// Функция после загрузки страницы
document.addEventListener('DOMContentLoaded', function() {

    // При отправки формы с .confirm-form будет подтвержение отправки
    document.addEventListener('submit', function(e) {
        const spinner = document.getElementById('spinner')

        if (e.target.classList.contains('confirm-form')) {
            e.preventDefault()

            const modal = document.getElementById('modal-confirm'),
                modalInstance = new Bootstrap.Modal(modal),
                btnOk = modal.querySelector('.btn-outline-primary')

            // Открыть модальное окно
            modalInstance.show()

            btnOk.addEventListener('click', function() {
                e.target.submit()
                spinner.style.display = 'block'
                modalInstance.hide()
            }.bind(e))
        }
    })


    // При клике по ссылке .confirm-link будет подтвержение отправки (добавить атрибуты data-toggle="modal" data-target="#modal-confirm")
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('confirm-link')) {
            e.preventDefault()

            const modal = document.getElementById('modal-confirm'),
                modalInstance = new Bootstrap.Modal(modal),
                btnOk = modal.querySelector('.btn-outline-primary'),
                href = e.target.href,
                spinner = document.getElementById('spinner')

            // Открыть модальное окно
            modalInstance.show()

            btnOk.addEventListener('click', function() {

                // Закрыть модальное окно
                modalInstance.hide()

                // Включить спинер
                if (spinner) {
                    spinner.style.display = 'block'
                }


                // Переход по ссылке
                document.location.href = href
            })
        }
    })

}, false)
