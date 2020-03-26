import Vue from 'vue';
import VueRouter from "vue-router";

Vue.use(VueRouter);

const auth = {
    user: {auth: true},
    agent: {auth: {roles: 'agent', redirect: {path: '/login'}, forbiddenRedirect: '/404'}},
    manager: {auth: {roles: 'manager', redirect: {path: '/login'}, forbiddenRedirect: '/404'}},
    admin: {auth: {roles: 'admin', redirect: {path: '/login'}, forbiddenRedirect: '/404'}},
    multiple(roles) {
        return {auth: {roles, redirect: {path: '/login'}, forbiddenRedirect: '/404'}};
    }
};

const router = new VueRouter({
    routes: [
        {
            path: '/',
            name: 'Home',
            meta: auth.user,
            component: () => import('../pages/History'),
        },
        {
            path: '/auth/login',
            name: 'Login',
            meta: {auth:false},
            component: () => import('../pages/Login'),
        },
        {
            path: '/auth/register',
            name: 'Register',
            meta: {auth:false},
            component: () => import('../pages/Register'),
        },
    ],
});

export default router;