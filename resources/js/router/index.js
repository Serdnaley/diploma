import Vue from 'vue';
import VueRouter from "vue-router";
import VueRouterBackButton from 'vue-router-back-button';
import {getQueryVariable} from "../util";

Vue.use(VueRouter);

const router = new VueRouter({
    routes: [
        {
            path: '/',
            name: 'Home',
            redirect: '/account',
        },
        {
            path: '/login',
            name: 'Login',
            component: require('../pages/Login').default,
            beforeEnter(to, from, next) {
                if (Vue.auth.check() && !getQueryVariable('auth_token'))
                    next('/');
                else
                    next();
            },
        },
        {
            path: '/account',
            name: 'Account',
            meta: {auth: true},
            component: require('../pages/account/Account').default,
            children: [
                {
                    path: 'report/add',
                    name: 'AccountAddReport',
                    component: require('../pages/reports/AddEditReport').default,
                },
                {
                    path: 'report/edit/:report_id',
                    name: 'AccountEditReport',
                    component: require('../pages/reports/AddEditReport').default,
                },
            ],
        },
        {
            path: '/reports',
            name: 'Reports',
            meta: {auth: true},
            component: require('../pages/reports/Reports').default,
            beforeEnter(to, from, next) {
                if (Vue.auth.check(['admin', 'manager'])) {
                    next();
                } else {
                    next({
                        name: 'Account' + to.name,
                        params: to.params,
                        query: to.query,
                    });
                }
            },
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
            path: '/users',
            name: 'Users',
            meta: {auth: ['admin', 'manager']},
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
            path: '/categories',
            name: 'Categories',
            meta: {auth: ['admin', 'manager']},
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
            path: '/register',
            name: 'Settings',
            meta: {auth: ['admin', 'manager']},
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
