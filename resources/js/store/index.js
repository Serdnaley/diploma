import Vue from 'vue';
import Vuex from 'vuex';

Vue.use(Vuex);

// Modules

import reports from './modules/reports';
import users from './modules/users';
import categories from './modules/categories';
import files from './modules/files';
import telegram_chats from './modules/telegram_chats';

export const store = new Vuex.Store({
    strict: process.env.NODE_ENV === 'development',
    modules: {
        reports,
        users,
        categories,
        files,
        telegram_chats,
    },
});

export default store;