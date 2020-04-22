import settingsObj from './settings'
import validator from './validator'

document.addEventListener('DOMContentLoaded', function() {

    const forms = document.querySelectorAll('form.form-post')
    if (forms[0]) {
        forms.forEach(function (form) {
            let name = form.getAttribute('name'), // Имя в теге form
                settings = settingsObj[name]
            validator(form, settings)
        })
    }

}, false)
