import Vue from 'vue'
import Vuelidate from 'vuelidate'
import axios from 'axios'
import lodash from 'lodash'
import moment from "moment"
import VueImg from 'v-img'
import ElementUI from 'element-ui'
import ElementUILocale from 'element-ui/lib/locale/lang/ru-RU'
import {loadProgressBar} from 'axios-progress-bar'

Vue.use(Vuelidate);
Vue.use(VueImg, {sourceButton: true, thumbnails: true});
Vue.use(ElementUI, {locale: ElementUILocale});

loadProgressBar();

moment.locale('ru');
moment.updateLocale('ru', {
    week: {
        dow: 1, // First day of week is Monday
        doy: 4  // First week of year must contain 4 January (7 + 1 - 4)
    }
});

window._ = lodash;

/**
 * We'll load the axios HTTP library which allows us to easily issue requests
 * to our Laravel back-end. This library automatically handles sending the
 * CSRF token as a header based on the value of the "XSRF" token cookie.
 */

window.axios = axios;
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';