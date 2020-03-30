import axios from 'axios';

export default {

    state: {
        categories: [],
        category: null,
    },

    getters: {
        categories: (state) => state.categories,
        category: (state) => state.category,
    },

    mutations: {
        get_categories: (state, data) => state.categories = data,
        add_category: (state, data) => {
            state.category = data;
            state.categories.push(data);
        },
        get_category: (state, data) => state.category = data,
        update_category: (state, data) => {
            if (state.category.id === data.id) state.category = data;
            let index = _.findIndex(state.categories, {id: data.id});
            if (index !== -1) state.categories[index] = data;
        },
        delete_category: (state, data) => {
            if (state.category.id === data.id) state.category = null;
            let index = _.findIndex(state.categories, {id: data.id});
            if (index !== -1) state.categories.splice(index, 1);
        },
    },

    actions: {

        async getCategories({ commit }, data = {}) {
            let res = await axios.get(`category`, data);

            commit('get_categories', res.data);

            return res;
        },

        async addCategory({ commit }, data = {}) {
            let res = await axios.post(`category`, data);

            commit('add_category', res.data);

            return res;
        },

        async getCategory({ commit }, data = {}) {
            let res = await axios.get(`category/${data.id}`, data);

            commit('get_category', res.data);

            return res;
        },

        async updateCategory({ commit }, data = {}) {
            let res = await axios.put(`category/${data.id}`, data);

            commit('update_category', res.data);

            return res;
        },

        async deleteCategory({ commit }, data) {
            let res = await axios.delete(`category/${data.id}`);

            commit('delete_category', data);

            return res;
        },

    },

}