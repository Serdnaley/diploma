require('./bootstrap');

window.Vue = require('vue');

import router from './router';
import store from './store';
import Layout from './pages/Layout';

new Vue({
    router,
    store,
    render: h => h(Layout),
}).$mount('#app');