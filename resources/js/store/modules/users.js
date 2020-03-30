import axios from 'axios';

export default {

    state: {
        users: '',
    },

    getters: {
        users: (state) => state.users,
    },

    mutations: {
        user_get: (state, data) => state.users = data,
    },

    actions: {

        async getUsers({ commit }, data = {}) {
            let res = await axios.get(`user`, data);

            commit('user_get', res.data);

            return res;
        },

    },

}