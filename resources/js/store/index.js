import Vue from 'vue';
import Vuex from 'vuex';

Vue.use(Vuex);

// Modules

import api from './modules/api';
import reports from './modules/reports';

export const store = new Vuex.Store({
    strict: process.env.NODE_ENV === 'development',
    modules: {
        api,
        reports,
    },
});

export default store;