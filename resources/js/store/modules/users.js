import axios from 'axios';

export default {

    state: {
        users: [],
        user: null,
    },

    getters: {
        users: (state) => state.users,
        user: (state) => state.user,
    },

    mutations: {
        get_users: (state, data) => state.users = data,
        add_user: (state, data) => {
            state.user = data;
            state.users.push(data);
        },
        get_user: (state, data) => state.user = data,
        update_user: (state, data) => {
            if (state.user.id === data.id) state.user = data;
            let index = _.findIndex(state.users, {id: data.id});
            if (index !== -1) state.users[index] = data;
        },
        delete_user: (state, data) => {
            if (state.user.id === data.id) state.user = null;
            let index = _.findIndex(state.users, {id: data.id});
            if (index !== -1) state.users.splice(index, 1);
        },
    },

    actions: {

        async getUsers({ commit }, data = {}) {
            let res = await axios.get(`user`, data);

            commit('get_users', res.data);

            return res;
        },

        async addUser({ commit }, data = {}) {
            let res = await axios.post(`user`, data);

            commit('add_user', res.data);

            return res;
        },

        async getUser({ commit }, data = {}) {
            let res = await axios.get(`user/${data.id}`, data);

            commit('get_user', res.data);

            return res;
        },

        async updateUser({ commit }, data = {}) {
            let res = await axios.put(`user/${data.id}`, data);

            commit('update_user', res.data);

            return res;
        },

        async deleteUser({ commit }, data) {
            let res = await axios.delete(`user/${data.id}`);

            commit('delete_user', data);

            return res;
        },

    },

}