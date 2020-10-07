import func from './functions'

// При клике на кнопку Вверх, движение вверх
$('#btn-up').click(function() {
    func.scrollUp()
})


/*
* Плавная прокрутка страницы до якоря.
* Добавить класс anchor и в href="#name_anchor" написать название якоря.
*/
$('.anchor').on('click', function(e) {
    e.preventDefault()
    var anchor = $(this).attr('href'),
        offset = 70

    if (anchor) {
        $('html, body').stop().animate({
            scrollTop: $(anchor).offset().top - offset
        }, 400)
    }
})

document.addEventListener('DOMContentLoaded', function() {



}, false)
