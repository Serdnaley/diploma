import axios from "axios";
import {queryString} from "../../util";

export default {

    state: {
        telegram_chats: [],
    },

    getters: {
        telegram_chats: (state) => state.telegram_chats,
    },

    mutations: {
        get_telegram_chats: (state, data) => state.telegram_chats = data,
    },

    actions: {

        async getTelegramChats({commit}, data) {
            let res = await axios.get(`telegram_chat?${queryString(data)}`);

            commit('get_telegram_chats', res.data.data);

            return res;
        },

    }
};
