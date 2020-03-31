import Vue from 'vue';
import VueRouter from "vue-router";
import {Loading} from "element-ui";
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
            component: () => import('../pages/Login'),
        },
        {
            path: '/categories',
            name: 'Categories',
            meta: {auth: 'admin'},
            component: () => import('../pages/categories/Categories'),
            children: [
                {
                    path: 'add',
                    name: 'AddCategory',
                    component: () => import('../pages/categories/AddEditCategory'),
                },
                {
                    path: 'edit/:category_id',
                    name: 'EditCategory',
                    component: () => import('../pages/categories/AddEditCategory'),
                },
            ],
        },
        {
            path: '/users',
            name: 'Users',
            meta: {auth: 'admin'},
            component: () => import('../pages/users/Users'),
            children: [
                {
                    path: 'add',
                    name: 'AddUser',
                    component: () => import('../pages/users/AddEditUser'),
                },
                {
                    path: 'edit/:user_id',
                    name: 'EditUser',
                    component: () => import('../pages/users/AddEditUser'),
                },
            ],
        },
        {
            path: '/reports',
            name: 'Reports',
            meta: {auth: 'admin'},
            component: () => import('../pages/reports/Reports'),
            children: [
                {
                    path: 'add',
                    name: 'AddReport',
                    component: () => import('../pages/reports/AddEditReport'),
                },
                {
                    path: 'edit/:report_id',
                    name: 'EditReport',
                    component: () => import('../pages/reports/AddEditReport'),
                },
            ],
        },
        {
            path: '/register',
            name: 'Settings',
            meta: {auth: 'admin'},
            component: () => import('../pages/settings/Settings'),
        },
        {
            path: '/403',
            name: '403',
            meta: {auth: true},
            component: () => import('../pages/403'),
        },
        {
            path: '*',
            name: '404',
            meta: {auth: true},
            component: () => import('../pages/404'),
        },
    ],
});

Vue.use(VueRouterBackButton, { router });

export default router;