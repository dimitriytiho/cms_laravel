
// Скрипты для Форм

// При клике на ссылку или кнопку добавиться disabled
document.addEventListener('click', function(e) {
    if (e.target.classList.contains('one-click')) {
        e.target.classList.add('disabled')
        //e.target.setAttribute('disabled', 'true')
    }
})


// При клике на ссылку или кнопку добавиться disabled
document.addEventListener('click', function(e) {
    if (e.target.classList.contains('spinner-click')) {
        const span = document.createElement('span')

        span.classList.add('spinner-grow', 'spinner-grow-sm', 'ml-1')
        e.target.appendChild(span)
    }
})
