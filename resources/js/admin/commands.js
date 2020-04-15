
// Функция после загрузки страницы
document.addEventListener('DOMContentLoaded', function() {

    // Блок commands
    const commands = document.querySelector('.command')
    if (commands) {
        const btn = commands.querySelector('form button'),
            preset = document.getElementById('preset-command'),
            commandInput = commands.querySelector('#full-command'),
            hidden = commands.querySelector('form input[type=hidden][name=command]'),
            start = 'php artisan '

        // Блокируется кнопка формы
        btn.classList.add('disabled')


        // При изменении select
        preset.addEventListener('change', function(e) {
            hidden.setAttribute('value', e.target.value)
            commandInput.value = start + e.target.value

            // Разблокируется кнопка формы
            btn.classList.remove('disabled')
        })


        // При изменении input
        commandInput.addEventListener('input', function(e) {
            hidden.setAttribute('value', e.target.value)

            // Разблокируется кнопка формы
            btn.classList.remove('disabled')
        })
    }

}, false)
