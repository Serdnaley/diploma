import Vue from 'vue'
import bearer from '@websanova/vue-auth/drivers/auth/bearer'
import axios from '@websanova/vue-auth/drivers/http/axios.1.x'
import router from '@websanova/vue-auth/drivers/router/vue-router.2.x'
import {MessageBox} from "element-ui";

const error = function (e) {
    if (e.response) {
        MessageBox('Ваша сессия истекла');
        Vue.auth.logout();
    } else {
        MessageBox('Проверьте ваше подключение к интернету.', 'Упс... Что-то пошло не так...');
    }
};

export default {
    auth: bearer,
    http: axios,
    router: router,
    tokenDefaultName: 'access',
    tokenStore: ['localStorage'],
    rolesVar: 'role',
    loginData: {url: 'auth/login', method: 'POST', redirect: '/', fetchUser: true},
    logoutData: {url: 'auth/logout', method: 'GET', redirect: '/login', makeRequest: true},
    fetchData: {url: 'auth/user', method: 'GET', enabled: true, error},
    refreshData: {url: 'auth/refresh', method: 'GET', enabled: true, interval: 30, error},
    parseUserData(data) {

        if (data.error) error();

        return data.data;
    }
}






