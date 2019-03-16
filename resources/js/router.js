import Vue from 'vue'

import VueRouter from 'vue-router'
Vue.use(VueRouter)

const files = require.context('./components', true, /\.vue$/)

files.keys().map(
    key => Vue.component(
        key.split('/').pop().split('.')[0],
        files(key).default
    )
)

const routes = [
    {
        name: 'home',
        path: '/',
        component: Vue.component('Home'),
    },
    {
        name: 'login',
        path: '/login',
        component: Vue.component('Login'),
    },
    {
        name: 'logout',
        path: '/logout',
    },
    {
        name: 'teams.create',
        path: '/teams/create',
        component: Vue.component('TeamsCreate'),
    },
    {
        name: 'teams.index',
        path: '/teams',
        component: Vue.component('TeamsIndex'),
    },
    {
        name: 'users.index',
        path: '/users',
        component: Vue.component('UsersIndex'),
    },
    {
        name: 'users.create',
        path: '/users/create',
        component: Vue.component('UsersCreate'),
    },
    {
        name: 'users.show',
        path: '/users/:id',
        component: Vue.component('UsersShow'),
        props: true,
    },
    {
        name: 'projects.show',
        path: '/:team/:id',
        component: Vue.component('ProjectsShow'),
        props: true,
    },
    {
        name: 'builds.show',
        path: '/:team/:project/:number',
        component: Vue.component('BuildsShow'),
        props: true,
    },
    {
        name: 'teams.show',
        path: '/:id',
        component: Vue.component('TeamsShow'),
        props: true,
    },
]

const router = new VueRouter({
    routes,
})

export default router
