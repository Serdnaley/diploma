import Vue from 'vue';

import './bootstrap';

import Layout from './pages/Layout';

import '../scss/style.scss';

new Vue({
    router: Vue.router,
    store: Vue.store,
    render: h => h(Layout),
}).$mount('#app');