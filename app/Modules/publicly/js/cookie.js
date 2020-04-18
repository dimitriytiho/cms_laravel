
export default {

    // Возвращает куки с указанным name, или false, если ничего не найдено.
    getCookie: function (name) {
        let matches = document.cookie.match(new RegExp(
            "(?:^|; )" + name.replace(/([\.$?*|{}\(\)\[\]\\\/\+^])/g, '\\$1') + "=([^;]*)"
        ))
        return matches ? decodeURIComponent(matches[1]) : false
    },


    /*
    * Устанавливает куку.
    * name - имя куки.
    * value - значение куки.
    * options - опции, например setCookie('user', 'John', {secure: true}), необязательный параметр.
    */
    setCookie: function (name, value, options = {}) {
        let isCookie = navigator.cookieEnabled,
            date = new Date(),
            dateCookie = new Date(date.getTime() + main.cookie)

        if (isCookie) {
            options = {
                path: '/',
                expires: dateCookie
                // При необходимости добавьте другие значения по-умолчанию
            }

            if (options.expires.toUTCString) {
                options.expires = options.expires.toUTCString()
            }

            let updatedCookie = encodeURIComponent(name) + "=" + encodeURIComponent(value)

            for (let optionKey in options) {
                updatedCookie += "; " + optionKey
                let optionValue = options[optionKey]
                if (optionValue !== true) {
                    updatedCookie += "=" + optionValue
                }
            }

            document.cookie = updatedCookie
        }
    },


    // Удалить куку с указанным name.
    deleteCookie: function (name) {
        setCookie(name, '', {
            'max-age': -1
        })
    }
}
