import IMask from 'imask'
import settingsObj from './settings'
import validator from './validator'

document.addEventListener('DOMContentLoaded', function() {

	// Маска ввода телефона
    const tel = document.querySelectorAll('form input[name=tel]')
    if (tel[0]) {
        const maskOptions = {
            mask: '+{7}(000)000-00-00'
            // lazy: false // Чтобы маска была сразу видна
        }
        tel.forEach(function (el) {
            const mask = IMask(el, maskOptions)
        })
    }
    

    const forms = document.querySelectorAll('form.form-post')
    if (forms[0]) {
        forms.forEach(function (form) {
            let name = form.getAttribute('name'), // Имя в теге form
                settings = settingsObj[name]
            validator(form, settings)
        })
    }

}, false)
