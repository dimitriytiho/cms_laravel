import axios from 'axios'
import message from './message'

// Функция после загрузки страницы
document.addEventListener('DOMContentLoaded', function() {

    const modal = document.getElementById('modal-confirm'),
        content = document.querySelector('.content'),
        spinner = document.getElementById('spinner')

    let btnOk = null

    if (modal) {
        btnOk = modal.querySelector('.btn-outline-primary')
    }



    // При клике на #slug-edit генерируется ссылка
    const slugEdit = document.getElementById('slug-edit')
    if (slugEdit) {
        slugEdit.addEventListener('click', function (e) {
            e.preventDefault()
            const title = document.querySelector('form input[name=title]')

            if (title) {

                if (spinner) {
                    spinner.style.display = 'block'
                }
                axios.post(main.url + '/cyrillic-to-latin', {
                    title: title.value
                })
                    .then(function (res) {
                        const slug = document.querySelector('form input[name=slug]')

                        if (slug) {
                            slug.value = res.data
                        }

                        if (spinner) {
                            spinner.style.display = 'none'
                        }
                    })
                    .catch(function (e) {
                        message.error(e)
                    })
            }
        })
    }


    // При клике на #transliterator транлитерируется текст
    const transliterator = document.getElementById('transliterator')
    if (transliterator) {
        transliterator.addEventListener('click', function (e) {
            const cyrillic = document.querySelector('.transliterator input[name=cyrillic]')

            if (cyrillic) {
                if (spinner) {
                    spinner.style.display = 'block'
                }

                axios.post(main.url + '/cyrillic-to-latin', {
                    title: cyrillic.value
                })
                    .then(function (res) {
                        const latin = document.querySelector('.transliterator input[name=latin]')
                        if (latin) {
                            latin.value = res.data
                        }

                        if (spinner) {
                            spinner.style.display = 'none'
                        }
                    })
                    .catch(function (e) {
                        message.error(e)
                    })
            }
        })
    }


    // При клике на #key-to-enter меняем ключ в БД
    const keyToEnter = document.getElementById('key-to-enter')
    if (keyToEnter) {

        // Значени input, которое было изначально
        let keyToEnterInputOld = document.querySelector('.key-to-enter input[name=to_change_key]')
        if (keyToEnterInputOld) {
            keyToEnterInputOld = keyToEnterInputOld.value
        }

        keyToEnter.addEventListener('click', function (e) {
            const keyToEnter = document.querySelector('.key-to-enter input[name=to_change_key]')

            if (keyToEnterInputOld && keyToEnter) {
                let keyToEnterValue = keyToEnter.value

                if (keyToEnterInputOld !== keyToEnterValue) {

                    // Минимум 6 символов
                    if (keyToEnterValue.length > 5) {

                        if (spinner) {
                            spinner.style.display = 'block'
                        }

                        axios.post(main.url + '/to-change-key', {
                            key: keyToEnterValue
                        })
                            .then(function (res) {

                                if (spinner) {
                                    spinner.style.display = 'none'
                                }

                                // Сообщение об успехе
                                message.success(res.data)
                            })
                            .catch(function (e) {
                                message.error(e)
                            })

                    } else {
                        message.error(translations['min6'])
                    }
                }
            }
        })
    }


    // При клике на #change-password-btn меняем пароль у пользователя
    const changePassword = document.getElementById('change-password-btn')
    if (changePassword) {

        changePassword.addEventListener('click', function(e) {
            e.preventDefault()

            var url = main.url + '/user-change-password',
                userID = e.target.dataset.userId,
                password = document.querySelector('input[name=password]'),
                confirm = document.querySelector('input[name=password_confirmation]')

            if (password && confirm) {
                if (password.value && confirm.value) {
                    if (password.value.toString().length > 5) {
                        if (password.value === confirm.value) {

                            if (spinner) {
                                spinner.style.display = 'block'
                            }

                            // Отправить post запрос
                            axios.post(url, {
                                userID: userID,
                                password: password.value
                            })
                                .then(function (res) {

                                    // При успешном ответе
                                    const changePasswordTrigger = document.querySelector('button[data-target="#change-password"]')
                                    if (changePasswordTrigger) {
                                        const collapseInit = new Bootstrap.Collapse(changePasswordTrigger),
                                            password = document.querySelector('input[name=password]'),
                                            confirm = document.querySelector('input[name=password_confirmation]')

                                        // Inputs password и confirm очистим значения
                                        if (password && confirm) {
                                            password.value = ''
                                            confirm.value = ''
                                        }

                                        // Закроем открытый collapse
                                        collapseInit.hide()
                                    }

                                    if (spinner) {
                                        spinner.style.display = 'none'
                                    }

                                    // Сообщение об успехе
                                    message.success(res.data)

                                })
                                .catch(function (e) {
                                    message.error(e)
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
            }
        })
    }


    // Удаление картинки по клику
    content.addEventListener('click', function(e) {

        const removeClass = 'img-remove'

        // Делегируем событие клик
        if (table && e.target && e.target.classList.contains(removeClass)) {

            e.preventDefault()
            e.stopPropagation()

            // Вызов модального окна
            if (e.target && modal && btnOk) {
                const modalInstance = new Bootstrap.Modal(modal),
                    img = e.target.dataset.img,
                    maxFiles = e.target.dataset.maxFiles

                if (img && maxFiles) {

                    // Открыть модальное окно
                    modalInstance.show()

                    btnOk.onclick = function() {

                        // Закрыть модальное окно
                        modalInstance.hide()

                        if (spinner) {
                            spinner.style.display = 'block'
                        }

                        // Отправить post запрос
                        axios.post(main.url + '/img-remove', {
                            table,
                            img,
                            maxFiles,
                            class: currentClass
                        })
                            .then(function (res) {

                                // Если одиночная загрузка картинок, то заменим название на название по-умолчанию
                                if (maxFiles <= 1) {
                                    const avatar = document.getElementById('avatar')

                                    e.target.parentElement.setAttribute('href', defaultImg)
                                    e.target.parentElement.querySelector('img').setAttribute('src', defaultImg)
                                    e.target.style.display = 'none'


                                    // Если меняется картинка пользователя, то заменим её в шапке сайта
                                    if (defaultImg && currentClass === 'User' && imgUploadID === curID && avatar) {
                                        avatar.setAttribute('src', defaultImg)
                                    }


                                    // Если множественная загрузка картинок, то удалим картинку
                                } else {
                                    e.target.parentElement.remove()
                                }

                                if (spinner) {
                                    spinner.style.display = 'none'
                                }

                                // Сообщение об успехе
                                message.success(res.data)

                            })
                            .catch(function (e) {
                                message.error(translations['error_occurred'])
                            })
                    }
                }
            }

        }
    })



    // Запускаем функцию добавления и удаления
    addBelongs('select-product-category', 'product-add-category', 'category-many-elements')
    addBelongs('select-product-filter', 'product-add-filter', 'filter-many-elements')


    /*
     * Функция для добавления и удаления (к примеру категория для товара).
     * idSelect - передать id select c категориями (задать data-belongs-id и data-belongs-title).
     * idBtn - передать id кнопки, которая появляется после измения select (задать с параметрами data-id, data-url-destroy, без параметров: data-belongs-id, data-belongs-title).
     * idAppend - передать id блока, в который вставить html новых категорий (в вставляемом блоке должен быть класс .many-elements__close).
     */
    function addBelongs(idSelect, idBtn, idAppend) {
        const modal = document.getElementById('modal-confirm'),
            spinner = document.getElementById('spinner'),
            select = document.getElementById(idSelect),
            btn = document.getElementById(idBtn),
            appendBlock = document.getElementById(idAppend)

        let btnOk = null

        if (modal) {
            btnOk = modal.querySelector('.btn-outline-primary')
        }

        if (select && btn && appendBlock) {

            // Для select
            select.addEventListener('change', function (e) {
                const id = this.value,
                    title = e.target.options[this.selectedIndex].dataset.title,
                    titleLang = e.target.options[this.selectedIndex].dataset.titleLang
                //title = e.target.options[this.selectedIndex].textContent // Чтобы получить текст option у select при событии change

                // Появление кнопки, задать data-belongs-id и data-belongs-title
                if (id != 0) {
                    btn.classList.remove('js-none')
                    btn.dataset.belongsId = id
                    btn.dataset.belongsTitle = title
                    btn.dataset.belongsTitleLang = titleLang
                } else {
                    btn.classList.add('js-none')
                    btn.dataset.belongsId = ''
                    btn.dataset.belongsTitle = ''
                    btn.dataset.belongsTitleLang = ''
                }
            })


            // Для btn
            btn.addEventListener('click', function (e) {
                e.preventDefault()
                const url = e.target.dataset.url,
                    urlDestroy = e.target.dataset.urlDestroy,
                    id = e.target.dataset.id,
                    belongsId = e.target.dataset.belongsId,
                    belongsTitle = e.target.dataset.belongsTitle,
                    belongsTitleLang = e.target.dataset.belongsTitleLang,
                    html = `<div class="mr-4 many-elements">
                            <span class="many-elements__text">${belongsTitleLang}</span>
                            <a data-url="${urlDestroy}" data-category-id="${belongsId}" class="text-primary many-elements__close cur">&times;</a>
                        </div>`

                if (url && belongsId != 0) {

                    if (spinner) {
                        spinner.style.display = 'block'
                    }

                    // Отправить post запрос
                    axios.post(url, {
                        productId: id,
                        belongsId: belongsId
                    })
                        .then(function (res) {

                            // Удалить кнопку
                            btn.classList.add('js-none')

                            // Добавить атрибут disabled к отправляемуму option
                            select.options[select.selectedIndex].setAttribute('disabled', true)

                            // Вставить html категории
                            appendBlock.innerHTML += html

                            if (spinner) {
                                spinner.style.display = 'none'
                            }

                            message.success(res.data)
                        })
                        .catch(function (e) {
                            message.error(e)
                        })
                }
            })


            // Для appendBlock, должен быть класс .many-elements__close
            appendBlock.addEventListener('click', function (e) {

                if (e.target.classList.contains('many-elements__close')) {
                    const el = e.target.parentNode,
                        url = e.target.dataset.url,
                        id = e.target.dataset.id,
                        belongsId = e.target.dataset.belongsId


                    // Вызов модального окна
                    if (modal && btnOk) {
                        const modalInstance = new Bootstrap.Modal(modal)

                        // Открыть модальное окно
                        modalInstance.show()
                        btnOk.addEventListener('click', function() {

                            // Закрыть модальное окно
                            modalInstance.hide()

                            //if (!belongsId) message.error(e)
                            if (spinner) {
                                spinner.style.display = 'block'
                            }

                            // Отправить post запрос
                            axios.post(url, {
                                productId: id,
                                belongsId: belongsId
                            })
                                .then(function (res) {

                                    // Если что-то пойдёт не так, то перезагрузим страницу
                                    if (res.data == 1) {
                                        document.location.href = document.location.href
                                    }

                                    // Удалить элемент
                                    el.remove()

                                    // Сообщение об успехе
                                    message.success(res.data)

                                    if (spinner) {
                                        spinner.style.display = 'none'
                                    }

                                    // Удалить атрибут disabled у option с отправляемой категорией
                                    if (select) {

                                        const options = select.childNodes
                                        if (options) {
                                            options.forEach(function (el) {
                                                if (el.value == belongsId) {
                                                    el.removeAttribute('disabled')
                                                    return
                                                }
                                            })
                                        }
                                    }

                                })
                                .catch(function (e) {
                                    message.error(e)
                                })
                        })
                    }
                }
            })
        }
    }


    // Каждые 30 секунд обновляем счётчик онлайн пользователей на сайте
    const onlineUsersCount = document.querySelectorAll('.online-users-count')
    if (onlineUsersCount[0]) {

        setInterval(function () {

            axios.post(main.url + '/online-users')
                .then(function (res) {
                    const onlineUsers = res.data,
                        listUsers = document.querySelector('.online-users-list')
                    let list = ''

                    if (onlineUsers) {

                        // Вставляем кол-во в счётчик
                        onlineUsersCount.forEach(function (el) {
                            el.innerHTML = Object.keys(onlineUsers).length
                        })

                        // Обновим список
                        if (listUsers) {
                            Object.keys(onlineUsers).forEach(function (key) {

                                if (onlineUsers[key]['id']) {
                                    list += `<div>${key} - <a href="${main.url}/user/${onlineUsers[key]['id']}/edit">${onlineUsers[key]['name']}</a></div>`
                                } else {
                                    list += `<div>${key}</div>`
                                }

                            })
                            listUsers.innerHTML = list
                        }

                    } else {

                        // Вставляем кол-во в счётчик
                        onlineUsersCount.forEach(function (el) {
                            el.innerHTML = '0'
                        })

                        // Обновим список
                        if (listUsers) {
                            listUsers.innerHTML = list
                        }
                    }

                })
                .catch()

        }, 30000)
    }


}, false)
