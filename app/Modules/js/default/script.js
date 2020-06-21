import func from './functions'

// При клике на кнопку Вверх, движение вверх
$('#btn-up').click(function() {
    func.scrollUp()
})


/*
* Плавная прокрутка страницы до якоря.
* Добавить класс anchor и в href="#name_anchor" написать название якоря.
*/
$('.anchor').click(function(e) {
    e.preventDefault()
    const anchor = $(this).attr('href')

    if (anchor) {
        $('html, body').stop().animate({
            scrollTop: $(anchor).offset().top
        }, 400)
    }
})

document.addEventListener('DOMContentLoaded', function() {



}, false)
