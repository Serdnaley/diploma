import Vue from 'vue'
import VueAuth from '@websanova/vue-auth'
import Vuelidate from 'vuelidate'
import VueAxios from 'vue-axios'
import axios from 'axios'
import lodash from 'lodash'
import moment from "moment"
import VueImg from 'v-img'
import ElementUI from 'element-ui'
import ElementUILocale from 'element-ui/lib/locale/lang/ua'
import {loadProgressBar} from 'axios-progress-bar'

import auth from './plugins/auth';

import router from './router';
import store from './store';

import 'axios-progress-bar/dist/nprogress.css'

Vue.axios = axios;
Vue.router = router;
Vue.store = store;

window._ = lodash;

window.axios = axios;

axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
axios.defaults.baseURL = `/api/`;

Vue.use(VueAxios, axios);
Vue.use(VueAuth, auth);
Vue.use(Vuelidate);
Vue.use(VueImg, {sourceButton: true, thumbnails: true});
Vue.use(ElementUI, {locale: ElementUILocale});

loadProgressBar();

moment.locale('uk');
moment.updateLocale('uk', {
    week: {
        dow: 1, // First day of week is Monday
        doy: 4  // First week of year must contain 4 January (7 + 1 - 4)
    }
});
