// import f from '../default/functions'

const widthScreen = window.innerWidth || document.body.clientWidth
const content = document.querySelector('#content')
const contentHeight = content.offsetHeight
const bottomBlock = document.querySelector('#bottom-block')


// f.showJS('#about-us > div')
// f.getHeight('prices__info--text')

// Height
if (widthScreen > height && contentHeight < height) {
    bottomBlock.style.height = height - contentHeight + 'px'
}


// Flips
/*const flips = document.querySelectorAll('.flip-card > div')
flips.forEach(function (el) {
    el.addEventListener('mouseenter', function(e) {
        e.target.children[0].classList.add('flip')
    })
    el.addEventListener('mouseleave', function(e) {
        e.target.children[0].classList.remove('flip')
    })
})*/


// Police police
/*
const cookie = document.querySelector('.cookie')
if (cookie) {
    cookie.style.opacity = 0
    cookie.classList.add('scale-out')
    setTimeout(function () {
        cookie.style.opacity = 1
        cookie.classList.remove('scale-out')
        cookie.classList.add('scale-in')
    }, 3600)
}
*/
