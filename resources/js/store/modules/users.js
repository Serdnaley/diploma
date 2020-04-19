import axios from 'axios';
import {queryString} from "../../util";

export default {

    state: {
        users: [],
        user: null,
        user_reports: [],
    },

    getters: {
        users: (state) => state.users,
        user: (state) => state.user,
        user_reports: (state) => state.user_reports,
    },

    mutations: {
        get_users: (state, data) => state.users = data,
        add_user: (state, data) => {
            state.user = data;
            state.users.push(data);
        },
        get_user: (state, data) => state.user = data,
        get_user_reports: (state, data) => state.user_reports = data,
        update_user: (state, data) => {
            if (state.user && state.user.id === data.id) state.user = data;
            let index = _.findIndex(state.users, {id: data.id});
            if (index !== -1) state.users[index] = data;
        },
        delete_user: (state, data) => {
            if (state.user && state.user.id === data.id) state.user = null;
            let index = _.findIndex(state.users, {id: data.id});
            if (index !== -1) state.users.splice(index, 1);
        },
    },

    actions: {

        async getUsers({ commit }, data = {}) {
            let res = await axios.get(`user?${queryString(data)}`);

            commit('get_users', res.data.data);

            return res;
        },

        async addUser({ commit }, data = {}) {
            let res = await axios.post(`user`, data);

            commit('add_user', res.data.data);

            return res;
        },

        async getUser({ commit }, data = {}) {
            let res = await axios.get(`user/${data.id}`);

            commit('get_user', res.data.data);

            return res;
        },

        async getUserReports({commit}, data = {}) {
            let res = await axios.get(`user/${data.id}/reports`);

            commit('get_user_reports', res.data.data);

            return res;
        },

        async updateUser({ commit }, data = {}) {
            let res = await axios.put(`user/${data.id}`, data);

            commit('update_user', res.data.data);

            return res;
        },

        async deleteUser({ commit }, data) {
            let res = await axios.delete(`user/${data.id}`);

            commit('delete_user', data);

            return res;
        },

    },

}