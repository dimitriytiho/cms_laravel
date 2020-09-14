import message from './message'

// Функция после загрузки страницы
document.addEventListener('DOMContentLoaded', function() {

    var modal = $('#modal-confirm'),
        btnOk = modal.find('.btn-outline-primary'),
        spinner = document.getElementById('spinner')




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
        var select = $('#' + idSelect),
            btn = $('#' + idBtn),
            appendBlock = $('#' + idAppend)


        if (select.length > 0 && btn.length > 0 && appendBlock.length > 0) {

            // Для select
            select.change(function () {
                var self = $(this),
                    id = self.val(),
                    title = self.find(':selected').data('title'),
                    titleLang = self.find(':selected').data('title-lang')
                // title = e.target.options[this.selectedIndex].dataset.title,

                // Появление кнопки, задать data-belongs-id и data-belongs-title
                if (id != 0) {

                    btn.attr('data-belongs-id', id)
                        .attr('data-belongs-title', title)
                        .attr('data-belongs-title-lang', titleLang)
                        .show()

                } else {

                    btn.hide()
                        .attr('data-belongs-id', '')
                        .attr('data-belongs-title', '')
                        .attr('data-belongs-title-lang', '')
                }
            })


            /*
             * Добавляем элемент.
             * Для btn отслеживаем клик.
             */
            $(document).on('click', '#' + idBtn, function(e) {
                e.preventDefault()
                var self = $(this),
                    url = self.data('url'),
                    urlDestroy = e.target.dataset.urlDestroy,
                    productId = e.target.dataset.id,
                    belongsId = e.target.dataset.belongsId,
                    belongsTitle = e.target.dataset.belongsTitle,
                    belongsTitleLang = e.target.dataset.belongsTitleLang,
                    html = `<div class="mr-4 many-elements">
                            <span class="many-elements__text">${belongsTitleLang}</span>
                            <a data-url="${urlDestroy}" data-id="${productId}" data-belongs-id="${belongsId}" class="text-primary many-elements__close cur">&times;</a>
                        </div>`

                if (url && productId && belongsId) {

                    $.ajax({
                        type: 'POST',
                        url: url,
                        data: {_token, productId, belongsId},
                        beforeSend: function() {

                            // Включаем спинер
                            spinner.style.display = 'block'
                        },
                        success: function(response) {

                            // Удалить кнопку
                            btn.hide()

                            // Добавить атрибут disabled к отправляемуму option
                            select.children('option[value=' + belongsId + ']').attr('disabled', true)
                            //select.options[select.selectedIndex].setAttribute('disabled', true)

                            // Вставить html категории
                            appendBlock.append(html)

                            // Выключаем спинер
                            spinner.style.display = 'none'

                            // Сообщение об успехе
                            message.success(response)
                        },
                        error: function () {
                            message.error(translations['something_went_wrong'])
                        }
                    })
                }
            })


            /*
             * Удаляем элемент.
             * Отслеживаем клик на .many-elements__close.
             * Для appendBlock, должен быть класс .many-elements__close
             */
            $(document).on('click', '.many-elements__close', function(e) {
                var self = $(this),
                    url = self.data('url'),
                    productId = self.data('id'),
                    belongsId = self.data('belongs-id')

                // Открыть модальное окно
                modal.modal()

                // Клик по кнопке
                btnOk.click(function () {

                    // Закрыть модальное окно
                    modal.modal('hide')

                    if (url && productId && belongsId) {

                        $.ajax({
                            type: 'POST',
                            url: url,
                            data: {_token, productId, belongsId},
                            beforeSend: function() {

                                // Включаем спинер
                                spinner.style.display = 'block'
                            },
                            success: function(response) {

                                // Если что-то пойдёт не так, то перезагрузим страницу
                                if (response == 1) {
                                    console.log(productId + ' ' + belongsId)
                                    //document.location.href = document.location.href
                                }

                                // Удалить элемент
                                self.parent().remove()

                                // Удалить атрибут disabled у option с отправляемой категорией
                                select.children('option[value=' + belongsId + ']').removeAttr('disabled')

                                // Выключаем спинер
                                spinner.style.display = 'none'

                                // Сообщение об успехе
                                message.success(response)
                            },
                            error: function () {
                                message.error(translations['something_went_wrong'])
                            }
                        })
                    }
                })
            })
        }
    }

}, false)
