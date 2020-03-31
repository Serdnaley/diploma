import axios from 'axios';
import moment from 'moment';
import {queryString} from "../../util";

export default {

    state: {
        reports: [],
        report: null,
    },

    getters: {
        reports: (state) => {
            let result = [];
            let used_ids = [];

            state.reports.map(user => {
                if (user.reports) {
                    user.reports.map(report => {
                        if (report.id && used_ids.includes(report.id)) {
                            return false;
                        }

                        report = _.clone(report);

                        report.user = _.cloneDeep(user);

                        if (!report.id) {
                            if (!report.date) {
                                report.status = 'new'
                            } else {
                                if (moment(report.date) < moment().subtract(1, 'year')) {
                                    report.status = 'expired'
                                } else {
                                    report.status = 'planned'
                                }
                            }
                        } else {
                            report.status = 'done'
                        }

                        used_ids.push(report.id);
                        result.push(report);
                    })
                }
            });

            return result;
        },
        report: (state) => state.report,

        reports_sorted_by_date: (state, getters) => {
            return [...getters.reports].sort((a,b) => moment(b.date) - moment(a.date))
        },

        reports_grouped_by_month: (state, getters) => {
            const result = [];
            let prev = false;

            getters.reports_sorted_by_date.map(report => {
                if (
                    !prev || (
                        moment(prev.date || 0).format('YYYY-MM')
                        !==
                        moment(report.date || 0).format('YYYY-MM')
                    )
                ) {
                    result.push({
                        date: report.date,
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
            let res = await axios.get(`report?${queryString(data)}`);

            commit('get_reports', res.data);

            return res;
        },

        async addReport({ commit }, data = {}) {
            let res = await axios.post(`report`, data);

            commit('add_report', res.data);

            return res;
        },

        async getReport({ commit }, data = {}) {
            let res = await axios.get(`report/${data.id}`);

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