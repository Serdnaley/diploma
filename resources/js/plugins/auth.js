import bearer from '@websanova/vue-auth/drivers/auth/bearer'
import axios from '@websanova/vue-auth/drivers/http/axios.1.x'
import router from '@websanova/vue-auth/drivers/router/vue-router.2.x'

// @todo remove cookies

const error = function () {
    Vue.auth.logout();

    alert('session expired');
};

export default {
    auth: bearer,
    http: axios,
    router: router,
    tokenDefaultName: 'access',
    tokenStore: ['localStorage'],
    rolesVar: 'role',
    loginData: {url: 'auth/login', method: 'POST', redirect: '/', fetchUser: true, error},
    logoutData: {url: 'auth/logout', method: 'POST', redirect: '/login', makeRequest: true, error},
    fetchData: {url: 'auth/user', method: 'GET', enabled: true, error},
    refreshData: {url: 'auth/refresh', method: 'GET', enabled: true, interval: 30, error},
    parseUserData(data) {

        if (data.error) error();

        return data.data;
    }
}






