import bearer from '@websanova/vue-auth/drivers/auth/bearer'
import axios from '@websanova/vue-auth/drivers/http/axios.1.x'
import router from '@websanova/vue-auth/drivers/router/vue-router.2.x'
import Cookies from 'js-cookie'

// @todo remove cookies

const error = function () {
    Vue.auth.logout();

    Cookies.remove('gamestore_session');
    Cookies.remove('XSRF-TOKEN');
    delete localStorage['laravel-vue-spa'];

    console.log('session expired');
};

export default {
    auth: bearer,
    http: axios,
    router: router,
    tokenDefaultName: 'laravel-vue-spa',
    tokenStore: ['localStorage'],
    rolesVar: 'role',
    registerData: {url: 'auth/register', method: 'POST', redirect: '', error},
    loginData: {url: 'auth/login', method: 'POST', redirect: '', fetchUser: true, error},
    logoutData: {url: 'auth/logout', method: 'POST', redirect: '/', makeRequest: true, error},
    fetchData: {url: 'auth/user', method: 'GET', enabled: true, error},
    refreshData: {url: 'auth/refresh', method: 'GET', enabled: true, interval: 30, error},
    parseUserData(data) {

        if (data.error) error();

        return data.data;
    }
}






