import func from './functions'


// Скрываем элементы с классами .js-hide
$('.js-hide').hide()

// Делаем видимыми элементы с классами .js-none-visible
$('.js-none-visible').show()

// Делаем видимыми элементы с классами .js-none-visible-flex
$('.js-none-visible-flex').css('display', 'flex')


// Отменяем обычное поведение ссылки при клике по класс .prevent-default
$('.prevent-default').click(function() {
    return false
})


// Открыть модальное окно по клику на класс .modal-show, при этом нужно указать здесь же атрибут data-modal-id="" и в него вписать id модального окна
document.addEventListener('click', function(e) {

    const modalShowClass = 'modal-show',
        block = e.target.classList.contains(modalShowClass) || e.target.closest('.' + modalShowClass) && e.target.closest('.' + modalShowClass).classList.contains(modalShowClass)

    if (block) {
        const modalId = e.target.dataset.modalId || e.target.closest('.' + modalShowClass).dataset.modalId

        if (modalId) {
            e.preventDefault()
            const modal = document.getElementById(modalId)

            if (modal) {
                const modalInit = new Bootstrap.Modal(modal)
                modalInit.show()
            }
        }
    }
})



// При клике добавляем класс .active к родителю
$('.click_add_active').click(function() {
    $(this).parent().addClass('active')
})

// При клике в любом месте убираем класс .active у блока с классом .click_remove_active
document.body.onclick = function(e) {
    const blockClass = 'remove_active',
        blocks = document.querySelectorAll('.' + blockClass),
        blockClick = e.target.classList.contains(blockClass) || e.target.closest('.' + blockClass)

    if (!blockClick) {
        blocks.forEach(function (el) {
            if (el.classList.contains('active')) {
                el.classList.remove('active')
            }
        })
    }
}


document.addEventListener('DOMContentLoaded', function() {

    // Одинаковая высота блоков, задать класс у элементов .height-math
    func.getHeight('height-math')

}, false)

