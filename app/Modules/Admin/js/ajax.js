import message from './message'


document.addEventListener('DOMContentLoaded', function() {

    var modal = $('#modal-confirm'),
        btnOk = modal.find('.btn-outline-primary'),
        spinner = document.getElementById('spinner')


    // При клике на #slug-edit генерируется ссылка
    $('#slug-edit').click(function (e) {
        e.preventDefault()
        var title = document.querySelector('form input[name=title]')
        if (title) {

            $.ajax({
                type: 'POST',
                url: main.url + '/cyrillic-to-latin',
                data: {_token: _token, title: title.value},
                beforeSend: function() {

                    // Включаем спинер
                    spinner.style.display = 'block'
                },
                success: function(response) {

                    // Вставляем ссылку
                    var slug = document.querySelector('form input[name=slug]')
                    if (slug) {
                        slug.value = response
                    }

                    // Выключаем спинер
                    spinner.style.display = 'none'
                },
                error: function () {
                    message.error(translations['something_went_wrong'])
                }
            })
        }
    })


    // Значени input, которое было изначально
    var keyToEnterInputOld = $('.key-to-enter input[name=to_change_key]').val()

    // При клике на #key-to-enter меняем ключ в БД
    $('#key-to-enter').click(function () {
        var keyToEnter = $('.key-to-enter input[name=to_change_key]').val()

        if (keyToEnterInputOld && keyToEnter && keyToEnterInputOld !== keyToEnter) {

            // Минимум 6 символов
            if (keyToEnter.toString().length > 5) {

                $.ajax({
                    type: 'POST',
                    url: main.url + '/to-change-key',
                    data: {_token: _token, key: keyToEnter},
                    beforeSend: function() {

                        // Включаем спинер
                        spinner.style.display = 'block'
                    },
                    success: function(response) {

                        // Выключаем спинер
                        spinner.style.display = 'none'

                        // Сообщение об успехе
                        message.success(response)
                    },
                    error: function () {
                        message.error(translations['something_went_wrong'])
                    }
                })

            } else {
                message.error(translations['min6'])
            }
        } else {
            message.error(translations['data_has_not_changed'])
        }
    })


    // При клике на #change-password-btn меняем пароль у пользователя
    $('#change-password-btn').click(function (e) {
        e.preventDefault()

        var self = $(this),
            userId = self.data('user-id'),
            password = self.closest('form').find('input[name=password]'),
            passwordVal = password.val(),
            confirm = self.closest('form').find('input[name=password_confirmation]'),
            confirmVal = confirm.val()

        if (passwordVal && confirmVal) {
            if (password.toString().length > 5) {
                if (passwordVal === confirmVal) {

                    $.ajax({
                        type: 'POST',
                        url: main.url + '/user-change-password',
                        data: {_token: _token, userId: userId, password: passwordVal},
                        beforeSend: function() {

                            // Включаем спинер
                            spinner.style.display = 'block'
                        },
                        success: function(response) {

                            // Закроем открытый collapse
                            self.closest('.collapse').prev('button').collapse('hide')

                            // Inputs password и confirm очистим значения
                            password.val('')
                            confirm.val('')

                            // Выключаем спинер
                            spinner.style.display = 'none'

                            // Сообщение об успехе
                            message.success(response)
                        },
                        error: function () {
                            message.error(translations['something_went_wrong'])
                        }
                    })

                } else {
                    message.error(translations['password_confirm_must_match'])
                }

            } else {
                message.error(translations['min6'])
            }
        } else {
            message.error(translations['data_has_not_changed'])
        }
    })


    // Удаление картинки по клику
    $(document).on('click', '.img-remove', function(e) {
        e.preventDefault()
        e.stopPropagation()
        var self = $(this),
            img = self.data('img'),
            maxFiles = self.data('max-files')

        if (img && maxFiles) {

            // Открыть модальное окно
            modal.modal()

            // Клик по кнопке
            btnOk.click(function () {

                // Закрыть модальное окно
                modal.modal('hide')

                $.ajax({
                    type: 'POST',
                    url: main.url + '/img-remove',
                    data: {
                        _token: _token,
                        table: table,
                        img: img,
                        maxFiles: maxFiles,
                        class: currentClass
                    },
                    beforeSend: function() {

                        // Включаем спинер
                        spinner.style.display = 'block'
                    },
                    success: function(response) {

                        // Если одиночная загрузка картинок, то заменим название на название по-умолчанию
                        if (maxFiles <= 1) {

                            // Меняем картинку
                            self.parent().attr('href', defaultImg)
                            self.next('img').attr('src', defaultImg)

                            // Прячем крестик удаления
                            self.hide()

                            // Если меняется картинка пользователя, то заменим её в шапке сайта
                            if (defaultImg && currentClass === 'User' && imgUploadID === curID) {
                                $('#avatar').attr('src', defaultImg)
                            }


                        // Если множественная загрузка картинок, то удалим картинку
                        } else {
                            self.parent().remove()
                        }

                        // Выключаем спинер
                        spinner.style.display = 'none'

                        // Сообщение об успехе
                        message.success(response)
                    },
                    error: function () {
                        message.error(translations['something_went_wrong'])
                    }
                })
            })
        }
    })

}, false)
