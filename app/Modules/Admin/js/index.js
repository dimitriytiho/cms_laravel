/*import bsn from 'bootstrap.native/dist/bootstrap-native-v4'
export default bsn*/
//window.Bootstrap = require('bootstrap.native/dist/bootstrap-native.min')

import IMask from 'imask'
import './add_remove_element'
import './ajax'
//import './native'
import './dropzone'
import './pulse'
import './forms'
import './scroll'
import './message'
//import './aside'
import './commands'
import './validate'
import './confirm'
import './dropdown'
import './scripts'

// Маска ввода телефона
var tel = document.querySelectorAll('form input[name=tel]'),
    maskOptions = {
    mask: '+{7}(000)000-00-00'
    // lazy: false // Чтобы маска была сразу видна
}
if (tel[0]) {
    tel.forEach(function (el) {
        const mask = IMask(el, maskOptions)
    })
}
