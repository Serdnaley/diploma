import Vue from 'vue';

import './bootstrap';

import router from './router';
import store from './store';
import Layout from './pages/Layout';

import '../scss/style.scss';

new Vue({
    router,
    store,
    render: h => h(Layout),
}).$mount('#app');