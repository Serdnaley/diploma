import Vue from 'vue';
import Vuex from 'vuex';

Vue.use(Vuex);

// Modules

import api from './modules/api';

export const store = new Vuex.Store({
    strict: process.env.NODE_ENV === 'development',
    modules: {
        api
    },
});

export default store;