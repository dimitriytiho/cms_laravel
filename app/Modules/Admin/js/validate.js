import f from './functions'
// import message from "./message";
// import libs from "../default/libs";

const formClass = 'needs-validation',
    form = document.querySelector('.' + formClass)

if (form) {
    const required = form.querySelectorAll('input[required], textarea[required]'),
        btn = form.querySelector('button[type=submit]'),
        inputs = f.serialize('.content form'),
        spinner = document.getElementById('spinner'),
        noSubmit = document.querySelector('.' + formClass + '.needs-validation-no-submit')

    let submit = true


    // Если есть класс .needs-validation-no-submit, блокируем кнопку отправки
    if (noSubmit) {
        btn.setAttribute('disabled', true)
    }


    // При клике на submit ключаем спинер
    if (btn && spinner) {
        btn.addEventListener('click', function(e) {
            spinner.style.display = 'block'
        })
    }


    // Проверка каждого input c required
    required.forEach(function (el) {

        // Если все input заполнены
        if (el.value || el.getAttribute('type') === 'checkbox' && !el.checked) {
            submit = false
        }

        // При потери фокуса
        el.addEventListener('blur', function(e) {

            if (!e.target.value || e.target.getAttribute('type') === 'checkbox' && !e.target.checked) {
                e.target.classList.add('is-invalid')
                submit = false

            } else {
                e.target.classList.remove('is-invalid')
                submit = true
            }


            // Разблокируем кнопку отправки
            if (noSubmit && submit) {
                btn.removeAttribute('disabled')
            }
        })
    })

    // Проверка, чтобы все input c required были заполнены, только тогда отправиться форма
    /*form.addEventListener('submit', function(e) {

        const newInputs = f.serialize('.content form')

        // Если ни один input не изменился, то форма не отправится (выведется сообщение и перезагрузится страница)
        if (f.diffArr(inputs, newInputs)) {
            e.preventDefault()
            message.info(translations['data_has_not_changed'], null, true)
        }

        if (submit) {
            const span = document.createElement('span')
            btn.classList.add('disabled')
            span.classList.add('spinner-grow', 'spinner-grow-sm', 'ml-1')
            btn.appendChild(span)

        } else {
            e.preventDefault()
            form.classList.add('was-validated')
            f.scrollUp()
        }
    })*/
}
