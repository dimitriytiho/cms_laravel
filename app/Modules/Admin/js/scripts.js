import tabSave from './tabs_save'


// Функция после загрузки страницы
document.addEventListener('DOMContentLoaded', function() {


    // При изменении .select-change select делается запрос
    const selectChange = document.getElementById('select-change')
    if (selectChange) {

        selectChange.addEventListener('change', function(e) {
            var action = e.target.dataset.action,
                val = e.target.value

            if (action && val) {
                window.location = action + '?value=' + val
            }
        })
    }


    // При collapse с классом .change-icon изменение иконки вверх вниз
    $('.change-icon').click(function () {
        var self = $(this),
            arrowUp = 'fa-angle-up',
            arrowDown = 'fa-angle-down'

        // Собыите открытие collapse
        self.addEventListener('show.bs.collapse', function(event) {
            self.children('i').removeClass(arrowDown).addClass(arrowUp)
        }, false)

        // Собыите закрытие collapse
        self.addEventListener('hide.bs.collapse', function(event) {
            self.children('i').removeClass(arrowUp).addClass(arrowDown)
        }, false)
    })


    // Сохраняем ранее открытую вкладку на странице редактирования
    tabSave('tabs-edit')
    tabSave('import-export')

    /*const tabsEdit = document.getElementById('tabs-edit'),
        tabEditClass = 'nav-link',
        tabEditSave = localStorage.getItem('tabEdit'),
        tabEditSaveID = document.getElementById(tabEditSave)

    // Если сохранено в LocalStorage id элемента, то установим класс active к этому элементу
    if (tabEditSave && tabsEdit) {
        const tabEditLinks = tabsEdit.querySelectorAll('.' + tabEditClass),
            tabEditPanels = document.querySelectorAll('.tab-pane')

        if (tabEditSaveID && tabEditLinks) {
            tabEditLinks.forEach(function (el) {
                el.classList.remove('active')

                if (el.id === tabEditSave) {
                    el.classList.add('active')
                }

            })
        }

        if (tabEditSaveID && tabEditPanels) {
            tabEditPanels.forEach(function (el) {
                el.classList.remove('active')
                el.classList.remove('show')

                if (el.id === tabEditSave + '-link') {
                    el.classList.add('show')
                    el.classList.add('active')
                }
            })
        }
    }

    // При клике на таб, запишем в LocalStorage id элемента
    if (tabsEdit) {
        tabsEdit.onclick = function(e) {
            if (e.target.classList.contains(tabEditClass)) {
                if (e.target.id) {
                    localStorage.setItem('tabEdit', e.target.id)
                }
            }
        }
    }*/


    /*const browserHeight = document.documentElement.clientHeight,
        headerHeight = document.querySelector('.header').offsetHeight,
        topPanelHeight = document.querySelector('.top-panel').offsetHeight,
        footerHeight = document.querySelector('.footer').offsetHeight,
        content = document.querySelector('.content'),
        px = 'px'

    // Задаётся высота блока с контентом, если она маленькая
    if (browserHeight > content.offsetHeight + headerHeight + topPanelHeight + footerHeight) {
        content.style.height = browserHeight - headerHeight - topPanelHeight - footerHeight + px
    }*/


}, false)
