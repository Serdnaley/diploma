import axios from 'axios';
import moment from 'moment';

export default {

    state: {
        reports: [],
        report: null,
    },

    getters: {
        reports: (state) => state.reports,
        report: (state) => state.report,

        reports_sorted_by_date: (state, getters) => {
            return [...getters.reports].sort((a,b) => moment(b.created_at) - moment(a.created_at))
        },

        reports_grouped_by_month: (state, getters) => {
            const result = [];
            let prev = false;

            getters.reports_sorted_by_date.map(report => {
                if (
                    !prev || (
                        moment(prev.created_at).format('YYYY-MM')
                        !==
                        moment(report.created_at).format('YYYY-MM')
                    )
                ) {
                    result.push({
                        created_at: report.created_at,
                        items: [
                            report
                        ],
                    });
                } else {
                    let last = result[result.length - 1];
                    last.items.push(report);
                }
                prev = report;
            });

            return result;
        },
    },

    mutations: {
        get_reports: (state, data) => state.reports = data,
        add_report: (state, data) => {
            state.report = data;
            state.reports.push(data);
        },
        get_report: (state, data) => state.report = data,
        update_report: (state, data) => {
            if (state.report.id === data.id) state.report = data;
            let index = _.findIndex(state.reports, {id: data.id});
            if (index !== -1) state.reports[index] = data;
        },
        delete_report: (state, data) => {
            if (state.report.id === data.id) state.report = null;
            let index = _.findIndex(state.reports, {id: data.id});
            if (index !== -1) state.reports.splice(index, 1);
        },
    },

    actions: {

        async getReports({ commit }, data = {}) {
            let res = await axios.get(`report`, data);

            commit('get_reports', res.data);

            return res;
        },

        async addReport({ commit }, data = {}) {
            let res = await axios.post(`report`, data);

            commit('add_report', res.data);

            return res;
        },

        async getReport({ commit }, data = {}) {
            let res = await axios.get(`report/${data.id}`, data);

            commit('get_report', res.data);

            return res;
        },

        async updateReport({ commit }, data = {}) {
            let res = await axios.put(`report/${data.id}`, data);

            commit('update_report', res.data);

            return res;
        },

        async deleteReport({ commit }, data) {
            let res = await axios.delete(`report/${data.id}`);

            commit('delete_report', data);

            return res;
        },

    },

}