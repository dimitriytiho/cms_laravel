import axios from 'axios'
import message from './message'

// Функция после загрузки страницы
document.addEventListener('DOMContentLoaded', function() {

    const modal = document.getElementById('modal-confirm'),
        btnOk = modal.querySelector('.btn-outline-primary'),
        content = document.querySelector('.content')


    // При клике на #slug-edit генерируется ссылка
    const slugEdit = document.getElementById('slug-edit')
    if (slugEdit) {
        slugEdit.addEventListener('click', function (e) {
            e.preventDefault()
            let title = document.querySelector('form input[name=title]').value

            axios.post(main.url + '/cyrillic-to-latin', {
                title
            })
                .then(function (res) {
                    document.querySelector('form input[name=slug]').setAttribute('value', res.data)
                })
                .catch(function (e) {
                    message.error(e)
                })
        })
    }


    // При клике на #transliterator транлитерируется текст
    const transliterator = document.getElementById('transliterator')
    if (transliterator) {
        transliterator.addEventListener('click', function (e) {
            let cyrillic = document.querySelector('.transliterator input[name=cyrillic]').value

            if (cyrillic) {
                axios.post(main.url + '/cyrillic-to-latin', {
                    title: cyrillic
                })
                    .then(function (res) {
                        document.querySelector('.transliterator input[name=latin]').setAttribute('value', res.data)
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
        const keyToEnterInputValue = document.querySelector('.key-to-enter input[name=to_change_key]').value

        keyToEnter.addEventListener('click', function (e) {
            let keyToEnterValue = document.querySelector('.key-to-enter input[name=to_change_key]').value

            if (keyToEnterInputValue !== keyToEnterValue) {

                // Минимум 6 символов
                if (keyToEnterValue.length > 5) {

                    axios.post(main.url + '/to-change-key', {
                        key: keyToEnterValue
                    })
                        .then(function (res) {

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
        })
    }


    // При клике на #select-product-category добавляется категория к товару
    const selectProductCategory = document.getElementById('select-product-category'),
        productAddCategoryBtn = document.getElementById('product-add-category')

    if (selectProductCategory && productAddCategoryBtn) {

        selectProductCategory.addEventListener('change', function (e) {
            const categoryID = this.value,
                categoryTitle = e.target.options[this.selectedIndex].dataset.title
                //categoryTitle = e.target.options[this.selectedIndex].textContent // Чтобы получить текст option у select при событии change

            // Появление кнопки, задать data-category-id и data-category-title
            if (categoryID != 0) {
                productAddCategoryBtn.classList.remove('js-none')
                productAddCategoryBtn.dataset.categoryId = categoryID
                productAddCategoryBtn.dataset.categoryTitle = categoryTitle
            } else {
                productAddCategoryBtn.classList.add('js-none')
                productAddCategoryBtn.dataset.categoryId = ''
                productAddCategoryBtn.dataset.categoryTitle = ''
            }
        })

        productAddCategoryBtn.addEventListener('click', function (e) {
            e.preventDefault()
            const url = e.target.dataset.url,
                urlDestroy = e.target.dataset.urlDestroy,
                productID = e.target.dataset.productId,
                categoryID = e.target.dataset.categoryId,
                categoryTitle = e.target.dataset.categoryTitle,
                divParent = document.getElementById('category-many-elements'),
                html = `<div class="mr-4 many-elements">
                            <span class="many-elements__text">${categoryTitle}</span>
                            <a data-url="${urlDestroy}" data-category-id="${categoryID}" class="text-primary many-elements__close cur">&times;</a>
                        </div>`

            if (url && categoryID != 0) {

                // Отправить post запрос
                axios.post(url, {
                    productID: productID,
                    categoryID: categoryID
                })
                    .then(function (res) {
                        if (selectProductCategory && divParent) {

                            // Удалить кнопку
                            if (productAddCategoryBtn) {
                                productAddCategoryBtn.classList.add('js-none')
                            }

                            // Добавить атрибут disabled к отправляемуму option
                            selectProductCategory.options[selectProductCategory.selectedIndex].setAttribute('disabled', true)

                            // Вставить html категории
                            divParent.innerHTML += html
                        }
                        message.success(res.data)
                    })
                    .catch(function (e) {
                        message.error(e)
                    })
            }
        })
    }


    // При клике на .many-elements__close удаляется категория у товара
    const productCategoryDestroy = document.getElementById('category-many-elements')
    if (productCategoryDestroy) {
        productCategoryDestroy.addEventListener('click', function (e) {
            if (e.target.classList.contains('many-elements__close')) {
                const el = e.target.parentNode,
                    url = e.target.dataset.url,
                    categoryID = e.target.dataset.categoryId

                // Вызов модального окна
                if (modal && btnOk) {
                    const modalInstance = new Bootstrap.Modal(modal)

                    // Открыть модальное окно
                    modalInstance.show()
                    btnOk.addEventListener('click', function() {

                        // Закрыть модальное окно
                        modalInstance.hide()

                        //if (!categoryID) message.error(e)

                        // Отправить post запрос
                        axios.post(url, {
                            categoryID: categoryID
                        })
                            .then(function (res) {

                                // Удалить элемент
                                el.remove()

                                // Сообщение об успехе
                                message.success(res.data)

                                // Удалить атрибут disabled у option с отправляемой категорией
                                if (selectProductCategory) {

                                    const options = selectProductCategory.childNodes
                                    if (options) {
                                        options.forEach(function (el) {
                                            if (el.value == categoryID) {
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


}, false)
