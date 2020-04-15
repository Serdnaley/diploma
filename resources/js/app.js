import Vue from 'vue';

import './bootstrap';

import App from './pages/App';

import '../scss/style.scss';

new Vue({
    router: Vue.router,
    store: Vue.store,
    render: h => h(App),
}).$mount('#app');