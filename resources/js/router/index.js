import Vue from 'vue';
import VueRouter from "vue-router";
import VueRouterBackButton from 'vue-router-back-button';

Vue.use(VueRouter);

const router = new VueRouter({
    routes: [
        {
            path: '/',
            name: 'Home',
            meta: {auth: 'admin'},
            redirect: '/reports',
        },
        {
            path: '/login',
            name: 'Login',
            meta: {auth: false},
            component: require('../pages/Login').default,
        },
        {
            path: '/categories',
            name: 'Categories',
            meta: {auth: 'admin'},
            component: require('../pages/categories/Categories').default,
            children: [
                {
                    path: 'add',
                    name: 'AddCategory',
                    component: require('../pages/categories/AddEditCategory').default,
                },
                {
                    path: 'edit/:category_id',
                    name: 'EditCategory',
                    component: require('../pages/categories/AddEditCategory').default,
                },
            ],
        },
        {
            path: '/users',
            name: 'Users',
            meta: {auth: 'admin'},
            component: require('../pages/users/Users').default,
            children: [
                {
                    path: 'add',
                    name: 'AddUser',
                    component: require('../pages/users/AddEditUser').default,
                },
                {
                    path: 'edit/:user_id',
                    name: 'EditUser',
                    component: require('../pages/users/AddEditUser').default,
                },
            ],
        },
        {
            path: '/reports',
            name: 'Reports',
            meta: {auth: 'admin'},
            component: require('../pages/reports/Reports').default,
            children: [
                {
                    path: 'add',
                    name: 'AddReport',
                    component: require('../pages/reports/AddEditReport').default,
                },
                {
                    path: 'edit/:report_id',
                    name: 'EditReport',
                    component: require('../pages/reports/AddEditReport').default,
                },
            ],
        },
        {
            path: '/register',
            name: 'Settings',
            meta: {auth: 'admin'},
            component: require('../pages/settings/Settings').default,
        },
        {
            path: '/403',
            name: '403',
            meta: {auth: true},
            component: require('../pages/403').default,
        },
        {
            path: '*',
            name: '404',
            meta: {auth: true},
            component: require('../pages/404').default,
        },
    ],
});

Vue.use(VueRouterBackButton, { router });

export default router;