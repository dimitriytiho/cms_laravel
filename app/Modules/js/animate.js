
// Функция после загрузки страницы
document.addEventListener('DOMContentLoaded', function() {


    // При клике на на .dropdown-click показывает меню dropdown
    let dropdownShow = false
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('dropdown-click')) {
            e.preventDefault()
            dropdownShow = !dropdownShow
            let menu = e.target.closest('.dropdown').querySelector('.dropdown-menu'),
                ms = 200 // Можно поменять время

            if (dropdownShow) {
                menu.style.display = 'block'
                menu.classList.remove('anime-to-center')
                menu.classList.add('anime-from-center')
            } else {
                menu.classList.remove('anime-from-center')
                menu.classList.add('anime-to-center')
                setTimeout(function () {
                    menu.style.display = 'none'
                }, ms)
            }
        }
    })

}, false)
