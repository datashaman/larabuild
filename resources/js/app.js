import './bootstrap'

import Vue from 'vue'

import BootstrapVue from 'bootstrap-vue'
Vue.use(BootstrapVue)

import VueVisible from 'vue-visible'
Vue.use(VueVisible)

import apolloProvider from './apollo'

// This must be done after the components are registered
import router from './router'

const app = new Vue({
    el: '#app',
    apolloProvider,
    router,
    render: h => h(Vue.component('App')),
})
