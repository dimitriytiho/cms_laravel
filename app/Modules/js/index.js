// require('./bootstrap');
// window.Vue = require('vue');

window.Bootstrap = require('bootstrap.native/dist/bootstrap-native-v4')

window.AOS = require('aos')
// data-aos="fade-up" fade-down-right flip-left zoom-in
AOS.init({
    duration: 500
})

import './components'
import './validate'
import './default'




// import Vue from 'vue'

/**
 * The following block of code may be used to automatically register your
 * Vue components. It will recursively scan this directory for the Vue
 * components and automatically register them with their "basename".
 *
 * Eg. ./components/ExampleComponent.vue -> <example-component></example-component>
 */

// const files = require.context('./', true, /\.vue$/i);
// files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key).default));
// Vue.component('example-component', require('./components/ExampleComponent.vue').default)

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

/*new Vue({
    el: '#app'
})*/
